<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\PostAttachmentResource;
use App\Http\Resources\PostResource;
use App\Models\Followers;
use App\Models\PostAttachments;
use App\Models\Posts;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */

    public function index(Request $request, User $user)
    {
        $isCurrentUserFollower = false;
        if (!Auth::guest()) {
            $isCurrentUserFollower = Followers::where('user_id', $user->id)
                ->where('follower_id', Auth::id())->exists();
        }
        $followersCount = Followers::where('user_id', $user->id)->count();

        $followers = $user->followers;
        $following = $user->followings;
        $photos = PostAttachments::query()
            ->where('mime', 'LIKE', 'image/%')
            ->where('created_by', $user->id)
            ->latest()
            ->get();

        $posts = Posts::postForTimeLine(Auth::id(), false)
            ->leftJoin('users AS u', 'u.pinned_post_id', 'posts.id')
            ->where('user_id', $user->id)
            ->orderBy('u.pinned_post_id', 'desc')->orderBy('posts.created_at', 'desc')
            ->paginate(10);
        $posts = PostResource::collection($posts);
        if ($request->wantsJson()) {
            return $posts;
        }

        return Inertia::render('Profile/View', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'success' => session('success'),
            'isCurrentUserFollower' => $isCurrentUserFollower,
            'followersCount' => $followersCount,
            'user' => new UserResource($user),
            'posts' => $posts,
            'followers' => UserResource::collection($followers),
            'followings' => UserResource::collection($following),
            'photos' => PostAttachmentResource::collection($photos)
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return to_route('profile', $request->user())->with('success', 'Profile updated.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updateImage(Request $request)
    {
        $data = $request->validate([
            'cover' => 'nullable|image|max:5123',
            'avatar' => 'nullable|image|max:5123',
        ]);
        $user = $request->user();
        $cover  = $data['cover'] ?? null;
        /** @var illuminate\Http\UploadedFile $cover */
        $avatar = $data['avatar'] ?? null;

        $success = '';
        if ($cover) {
            if ($user->cover_path) {
                Storage::disk('public')->delete($user->cover_path);
            }
            $path = $cover->store('user-' . $user->id, 'public');
            $user->update([
                'cover_path' => $path
            ]);
            $success = 'Your cover has been updated!';
        }
        if ($avatar) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $path = $avatar->store('user-' . $user->id, 'public');
            $user->update([
                'avatar_path' => $path
            ]);
            $success = 'Your avatar has been updated!';
        }

        return back()->with('success', $success);
    }
}
