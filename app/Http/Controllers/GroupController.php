<?php

namespace App\Http\Controllers;

use App\Http\Enums\GroupUserRole;
use App\Http\Enums\GroupUserStatus;
use App\Models\Groups;
use App\Http\Requests\StoreGroupsRequest;
use App\Http\Requests\UpdateGroupsRequest;
use App\Http\Resources\GroupResource;
use App\Models\GroupUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function profile(Groups $group)
    {
        $group->load('currentUserGroup');
        return Inertia::render('Group/View', [
            'success' => session('success'),
            'group' => new GroupResource($group)
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
        //
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
        if($group->isAdmin(Auth::id() ?? null) === false) {
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
            if($group->cover_path) {
                Storage::disk('public')->delete($group->cover_path);
            }
            $path = $cover->store('group-' . $group->id, 'public');
            $group->update([
                'cover_path' => $path
            ]);
            $success = 'Your cover has been updated!';
        }
        if ($thumbnail) {
            if($group->thumbnail_path) {
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
}
