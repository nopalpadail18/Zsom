<?php

namespace App\Http\Controllers;

use App\Http\Enums\GroupUserRole;
use App\Http\Enums\GroupUserStatus;
use App\Http\Requests\InviteUsersRequest;
use App\Models\Groups;
use App\Http\Requests\StoreGroupsRequest;
use App\Http\Requests\UpdateGroupsRequest;
use App\Http\Resources\GroupResource;
use App\Http\Resources\GroupUserResource;
use App\Http\Resources\PostAttachmentResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\GroupUsers;
use App\Models\PostAttachments;
use App\Models\Posts;
use App\Models\User;
use App\Notifications\InvitationApproved;
use App\Notifications\InvitationInGroup;
use App\Notifications\RequestApproved;
use App\Notifications\RequestToJoinGroup;
use App\Notifications\RoleChanged;
use App\Notifications\UserRemoveFromGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function profile(Request $request, Groups $group)
    {
        $group->load('currentUserGroup');

        $userId = Auth::id();

        if ($group->hasApprovedUser($userId)) {
            $posts = Posts::postForTimeLine($userId, false)
                ->leftJoin('groups AS g', 'g.pinned_post_id', 'posts.id')
                ->where('group_id', $group->id)
                ->orderBy('g.pinned_post_id', 'desc')->orderBy('posts.created_at', 'desc')
                ->paginate(10);

            $posts = PostResource::collection($posts);
        } else {
            return  Inertia::render('Group/View', [
                'success' => session('success'),
                'group' => new GroupResource($group),
                'posts' => null,
                'users' => [],
                'requests' => []
            ]);
        }

        if ($request->wantsJson()) {
            return $posts;
        }

        $users = User::query()
            ->select(['users.*', 'gu.role', 'gu.status', 'gu.group_id'])
            ->join('group_users as gu', 'gu.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->where('gu.group_id', $group->id)
            ->get();
        $requests = $group->pendingUsers()->orderBy('name')->get();

        $photos = PostAttachments::query()
            ->select('post_attachments.*')
            ->join('posts AS p', 'p.id', '=', 'post_attachments.post_id')
            ->where('p.group_id', $group->id)
            ->where('mime', 'LIKE', 'image/%')
            ->latest()
            ->get();


        return Inertia::render('Group/View', [
            'success' => session('success'),
            'group' => new GroupResource($group),
            'posts' => $posts,
            'users' => GroupUserResource::collection($users),
            'requests' => UserResource::collection($requests),
            'photos' => PostAttachmentResource::collection($photos)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroupsRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $group = Groups::create($data);

        $groupUserData = [
            'status' => GroupUserStatus::APPROVED->value,
            'role'   => GroupUserRole::ADMIN->value,
            'user_id' => Auth::id(),
            'group_id' => $group->id,
            'created_by' => Auth::id(),
        ];

        GroupUsers::create($groupUserData);
        $group->status = $groupUserData['status'];
        $group->role = $groupUserData['role'];

        return response(new GroupResource($group), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupsRequest $request, Groups $groups)
    {
        $groups->update($request->validated());

        return back()->with('success', 'Your group has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Groups $groups)
    {
        //
    }

    public function updateImage(Request $request, Groups $group)
    {
        if ($group->isAdmin(Auth::id() ?? null) === false) {
            return response('You are not allowed to update this group\'s image', 403);
        }

        $data = $request->validate([
            'cover' => 'nullable|image|max:1024',
            'thumbnail' => 'nullable|image|max:1024',
        ]);


        $cover  = $data['cover'] ?? null;
        /** @var illuminate\Http\UploadedFile $cover */
        $thumbnail = $data['thumbnail'] ?? null;

        $success = '';
        if ($cover) {
            if ($group->cover_path) {
                Storage::disk('public')->delete($group->cover_path);
            }
            $path = $cover->store('group-' . $group->id, 'public');
            $group->update([
                'cover_path' => $path
            ]);
            $success = 'Your cover has been updated!';
        }
        if ($thumbnail) {
            if ($group->thumbnail_path) {
                Storage::disk('public')->delete($group->thumbnail_path);
            }
            $path = $thumbnail->store('group-' . $group->id, 'public');
            $group->update([
                'thumbnail_path' => $path
            ]);
            $success = 'Your thumbnail has been updated!';
        }

        return back()->with('success', $success);
    }

    public function inviteUsers(InviteUsersRequest $request, Groups $group)
    {
        $data  = $request->validated();

        $user = $request->user;
        $groupUser = $request->groupUsers;
        $hours = 24;
        $token = Str::random(256);

        if ($groupUser) {
            $groupUser->delete();
        }

        GroupUsers::create([
            'status' => GroupUserStatus::PENDING->value,
            'role' => GroupUserRole::USER->value,
            'token' => $token,
            'token_expire_date' => now()->addHours($hours),
            'user_id' => $user->id,
            'group_id' => $group->id,
            'created_by' => Auth::id(),
        ]);

        $user->notify(new InvitationInGroup($group, $hours, $token));

        return back()->with('success', 'User was invited to join to group');
    }

    public function approveInvitation(string $token)
    {
        $groupUser = GroupUsers::query()->where('token', $token)->first();

        $errorTitle = '';
        if (!$groupUser) {
            $errorTitle = 'The link is not valid';
        } else if ($groupUser->token_used || $groupUser->status === GroupUserStatus::APPROVED->value) {
            $errorTitle = 'The link has been used';
        } else if ($groupUser->token_expire_date < now()) {
            $errorTitle = 'The link has expired';
        }

        if ($errorTitle) {
            return \inertia('Error', compact('errorTitle'));
        }

        $groupUser->status = GroupUserStatus::APPROVED->value;
        $groupUser->token_used = now();
        $groupUser->save();

        $adminUser = $groupUser->adminUser;

        $adminUser->notify(new InvitationApproved($groupUser->group, $groupUser->user));

        return redirect(route('group.profile', $groupUser->group))
            ->with('success', 'You accepted to join to group "' . $groupUser->group->name . '"');
    }

    public function join(Groups $group)
    {
        $user = request()->user();

        $successMessage = 'You joined to group "' . $group->name . '"';
        $status = GroupUserStatus::APPROVED->value;
        if (!$group->auto_approval) {
            $status = GroupUserStatus::PENDING->value;
            Notification::send($group->adminUsers, new RequestToJoinGroup($group, $user));
            $successMessage = 'You request has been accepted. You will be notified once you will be approved';
        }

        GroupUsers::create([
            'status' => $status,
            'role' => GroupUserRole::USER->value,
            'user_id' => $user->id,
            'group_id' => $group->id,
            'created_by' => $user->id,
        ]);

        return back()->with('success', $successMessage);
    }

    public function approveRequests(Request $request, Groups $group)
    {
        if ($group->isAdmin(Auth::id() ?? null) === false) {
            return response('You are not accept to this user', 403);
        }

        $data = $request->validate([
            'user_id' => ['required'],
            'action'  => ['required']
        ]);

        $groupUser = GroupUsers::where('user_id', $data['user_id'])
            ->where('group_id', $group->id)
            ->where('status', GroupUserStatus::PENDING->value)
            ->first();

        if ($groupUser) {
            $approved = false;
            if ($data['action'] === 'approve') {
                $approved = true;
                $groupUser->status = GroupUserStatus::APPROVED->value;
            } else {
                $groupUser->status = GroupUserStatus::REJECTED->value;
            }

            $groupUser->save();

            $user = $groupUser->user;

            $user->notify(new RequestApproved($groupUser->group, $user, $approved));

            return back()->with('success', 'User "' . $user->name . '" was ' . ($approved ? 'approved' : 'rejected'));
        }

        return back();
    }

    public function removeUser(Request $request, Groups $group)
    {
        if ($group->isAdmin(Auth::id() ?? null) === false) {
            return response('You dont have permissions to do this action', 403);
        }

        $data = $request->validate([
            'user_id' => ['required']
        ]);

        $userId = $data['user_id'];

        if ($group->isOwner($userId)) {
            return response('The owner of the group cannot be removed', 403);
        }

        $groupUser = GroupUsers::where('user_id', $userId)
            ->where('group_id', $group->id)
            ->first();

        if ($groupUser) {
            $user = $groupUser->user;

            $groupUser->delete();

            $user->notify(new UserRemoveFromGroup($group));
        }

        return back();
    }

    public function changeRole(Request $request, Groups $group)
    {
        if ($group->isAdmin(Auth::id() ?? null) === false) {
            return response('You dont have permissions to do this action', 403);
        }

        $data = $request->validate([
            'user_id' => ['required'],
            'role'  => ['required', Rule::enum(GroupUserRole::class)]
        ]);

        $userId = $data['user_id'];

        if ($group->isOwner($userId)) {
            return response('You cant change your role', 403);
        }

        $groupUser = GroupUsers::where('user_id', $userId)
            ->where('group_id', $group->id)
            ->first();

        if ($groupUser) {
            $groupUser->role = $data['role'];

            $groupUser->save();

            $groupUser->user->notify(new RoleChanged($group, $data['role']));
        }

        return back();
    }
}
