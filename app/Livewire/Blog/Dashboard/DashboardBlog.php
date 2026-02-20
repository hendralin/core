<?php

namespace App\Livewire\Blog\Dashboard;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Livewire\Component;
use App\Models\PostView;
use Illuminate\Support\Facades\DB;

class DashboardBlog extends Component
{
    public function render()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('superadmin') || $user->hasRole('admin') || $user->hasRole('editor');

        $postsQuery = $isAdmin ? Post::query() : Post::where('user_id', $user->id);

        $stats = [
            'total_posts' => (clone $postsQuery)->count(),
            'published_posts' => (clone $postsQuery)->where('status', 'published')->count(),
            'draft_posts' => (clone $postsQuery)->where('status', 'draft')->count(),
            'total_views' => (clone $postsQuery)->sum('views_count'),
            'total_comments' => $isAdmin
                ? Comment::count()
                : Comment::whereHas('post', fn ($q) => $q->where('user_id', $user->id))->count(),
            'total_users' => $isAdmin ? User::count() : null,
        ];

        $mostViewedPosts = (clone $postsQuery)
            ->where('status', 'published')
            ->orderBy('views_count', 'desc')
            ->take(5)
            ->get();

        $recentComments = Comment::with(['user', 'post'])
            ->when(!$isAdmin, fn ($q) => $q->whereHas('post', fn ($query) => $query->where('user_id', $user->id)))
            ->latest()
            ->take(5)
            ->get();

        $rawViewsData = PostView::select(
            DB::raw('DATE(viewed_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->when(!$isAdmin, fn ($q) => $q->whereHas('post', fn ($query) => $query->where('user_id', $user->id)))
            ->where('viewed_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $viewsData = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateKey = $date->format('Y-m-d');
            $row = $rawViewsData->get($dateKey);
            $viewsData->push([
                'date' => $date->format('M d'),
                'count' => $row ? $row->count : 0,
            ]);
        }

        return view('livewire.blog.dashboard.dashboard-blog', [
            'stats' => $stats,
            'mostViewedPosts' => $mostViewedPosts,
            'recentComments' => $recentComments,
            'viewsData' => $viewsData,
            'isAdmin' => $isAdmin,
        ]);
    }
}
