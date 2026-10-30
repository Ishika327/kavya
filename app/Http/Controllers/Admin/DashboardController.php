<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Like;
use App\Models\User;
use App\Models\Writing;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $statistics = [
            'writings' => Writing::query()->count(),
            'publishedWritings' => Writing::query()->published()->count(),
            'categories' => Category::query()->count(),
            'users' => User::query()->count(),
            'likes' => Like::query()->count(),
            'views' => Writing::query()->sum('view_count'),
            'comments' => Comment::query()->count(),
        ];

        $recentWritings = Writing::query()
            ->select(['id', 'category_id', 'author_id', 'title', 'status', 'created_at'])
            ->with([
                'category:id,name',
                'author:id,name',
            ])
            ->latest()
            ->latest('id')
            ->limit(5)
            ->get();

        $recentUsers = User::query()
            ->select(['id', 'name', 'email', 'role', 'created_at'])
            ->latest()
            ->latest('id')
            ->limit(5)
            ->get();

        $recentComments = Comment::query()
            ->select(['id', 'user_id', 'writing_id', 'content', 'status', 'created_at'])
            ->with([
                'user:id,name',
                'writing:id,title',
            ])
            ->latest()
            ->latest('id')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'statistics',
            'recentWritings',
            'recentUsers',
            'recentComments',
        ));
    }
}
