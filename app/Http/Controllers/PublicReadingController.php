<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ReadingHistory;
use App\Models\Writing;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PublicReadingController extends Controller
{
    public function home(): View
    {
        $published = Writing::query()
            ->published()
            ->with(['category:id,name,slug', 'author:id,name']);

        $featuredWritings = (clone $published)
            ->where('is_featured', true)
            ->latest('published_at')
            ->latest('id')
            ->limit(3)
            ->get();

        $latestWritings = (clone $published)
            ->latest('published_at')
            ->latest('id')
            ->limit(6)
            ->get();

        $popularWritings = (clone $published)
            ->orderByDesc('view_count')
            ->orderByDesc('like_count')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $poemOfDay = (clone $published)
            ->where('is_poem_of_day', true)
            ->first();

        $poemOfWeek = (clone $published)
            ->where('is_poem_of_week', true)
            ->first();

        $categories = Category::query()
            ->withCount(['writings' => fn ($query) => $query->published()])
            ->orderBy('name')
            ->limit(8)
            ->get();

        return view('home', compact(
            'featuredWritings',
            'latestWritings',
            'popularWritings',
            'poemOfDay',
            'poemOfWeek',
            'categories',
        ));
    }

    public function explore(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'exists:categories,slug'],
            'language' => ['nullable', 'string', Rule::in(['en', 'ne'])],
            'sort' => ['nullable', 'string', Rule::in(['latest', 'popular'])],
        ]);
        $search = trim($validated['q'] ?? '');
        $categorySlug = $validated['category'] ?? '';
        $language = $validated['language'] ?? '';
        $sort = $validated['sort'] ?? 'latest';

        $writings = Writing::query()
            ->published()
            ->with(['category:id,name,slug', 'author:id,name'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('title', 'like', '%'.$search.'%')
                        ->orWhere('content', 'like', '%'.$search.'%');
                });
            })
            ->when($categorySlug !== '', fn ($query) => $query->whereHas(
                'category',
                fn ($categoryQuery) => $categoryQuery->where('slug', $categorySlug),
            ))
            ->when($language !== '', fn ($query) => $query->where('language', $language))
            ->when(
                $sort === 'popular',
                fn ($query) => $query
                    ->orderByDesc('view_count')
                    ->orderByDesc('like_count')
                    ->orderByDesc('id'),
                fn ($query) => $query
                    ->latest('published_at')
                    ->latest('id'),
            )
            ->paginate(12)
            ->withQueryString();

        $categories = Category::query()->orderBy('name')->get(['id', 'name', 'slug']);

        return view('explore', compact(
            'writings',
            'categories',
            'search',
            'categorySlug',
            'language',
            'sort',
        ));
    }

    public function categories(): View
    {
        $categories = Category::query()
            ->withCount(['writings' => fn ($query) => $query->published()])
            ->orderBy('name')
            ->get();

        return view('categories', compact('categories'));
    }

    public function category(Category $category): View
    {
        $writings = $category->writings()
            ->published()
            ->with(['category:id,name,slug', 'author:id,name'])
            ->latest('published_at')
            ->latest('id')
            ->paginate(12);

        return view('category', compact('category', 'writings'));
    }

    public function show(Writing $writing): View
    {
        abort_unless($writing->status === 'published' && $writing->published_at !== null, 404);

        $writing->increment('view_count');
        if (request()->user()) {
            ReadingHistory::query()->updateOrCreate(
                ['user_id' => request()->user()->id, 'writing_id' => $writing->id],
                ['last_read_at' => now()],
            );
        }

        $writing->load([
            'category:id,name,slug',
            'author:id,name',
            'comments' => fn ($query) => $query
                ->where('status', 'approved')
                ->with('user:id,name')
                ->latest(),
        ]);
        $isLiked = request()->user()?->likes()->whereBelongsTo($writing)->exists() ?? false;
        $isFavorited = request()->user()?->favorites()->whereBelongsTo($writing)->exists() ?? false;

        $relatedWritings = Writing::query()
            ->published()
            ->where('category_id', $writing->category_id)
            ->whereKeyNot($writing->id)
            ->with(['category:id,name,slug', 'author:id,name'])
            ->latest('published_at')
            ->latest('id')
            ->limit(3)
            ->get();

        return view('writing', compact('writing', 'relatedWritings', 'isLiked', 'isFavorited'));
    }
}
