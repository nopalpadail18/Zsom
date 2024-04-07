<?php

namespace App\Http\Controllers;

use App\Http\Resources\GroupResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Groups;
use App\Models\Posts;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function search(Request $request, string $search = null)
    {
        if (!$search) {
            return redirect('/');
        }

        $users = User::query()
            ->where('name', 'LIKE', "%$search%")
            ->orWhere('username', 'LIKE', "%$search%")
            ->orWhere('email', 'LIKE', "%$search%")
            ->latest()
            ->get();
        $groups = Groups::query()->where('name', 'LIKE', "%$search%")
            ->orWhere('about', 'LIKE', "%$search%")
            ->latest()
            ->get();
        $posts = Posts::postForTimeLine(Auth::id())
            ->where('body', 'LIKE', "%$search%")
            ->paginate(10);

        $posts = PostResource::collection($posts);
        if ($request->wantsJson()) {
            return $posts;
        }


        return inertia('Search', [
            'users' => UserResource::collection($users),
            'groups' => GroupResource::collection($groups),
            'posts' => $posts,
            'search' => $search
        ]);
    }
}
