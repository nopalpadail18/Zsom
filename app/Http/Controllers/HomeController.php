<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Posts;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $posts = Posts::query()->latest()->paginate(20);
        return Inertia::render('Home', [
            'posts' => PostResource::collection($posts)
        ]);
    }
}
