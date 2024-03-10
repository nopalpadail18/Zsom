<?php

namespace App\Http\Controllers;

use App\Http\Enums\GroupUserStatus;
use App\Http\Resources\GroupResource;
use App\Http\Resources\PostResource;
use App\Models\Groups;
use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $posts = Posts::query()
            ->withCount('reactions')
            ->with([
                'comments' => function ($q) use ($userId) {
                    $q->withCount('reactions');
                },
                'reactions' => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                }
            ])
            ->latest()->paginate(10);

        $posts = PostResource::collection($posts);
        if($request->wantsJson()){
            return $posts;
        }

        $groups = Groups::query()
            ->select(['groups.*', 'gu.status', 'gu.role'])
            ->join('group_users as gu', 'gu.group_id', 'groups.id')
            ->where('gu.user_id', $userId)
            ->orderBy('gu.role')
            ->orderBy('name', 'desc')
            ->get();

        return Inertia::render('Home', [
            'posts' => $posts,
            'groups' => GroupResource::collection($groups)
        ]);
    }
}
