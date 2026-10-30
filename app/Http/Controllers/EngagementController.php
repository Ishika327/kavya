<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Like;
use App\Models\Writing;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EngagementController extends Controller
{
    public function like(Request $request, Writing $writing): RedirectResponse
    {
        abort_unless($writing->status === 'published' && $writing->published_at !== null, 404);

        DB::transaction(function () use ($request, $writing): void {
            $like = Like::query()
                ->where('user_id', $request->user()->id)
                ->where('writing_id', $writing->id)
                ->first();

            if ($like) {
                $like->delete();
                $writing->decrement('like_count');

                return;
            }

            Like::query()->create([
                'user_id' => $request->user()->id,
                'writing_id' => $writing->id,
            ]);
            $writing->increment('like_count');
        });

        return back();
    }

    public function favorite(Request $request, Writing $writing): RedirectResponse
    {
        abort_unless($writing->status === 'published' && $writing->published_at !== null, 404);

        $favorite = Favorite::query()
            ->where('user_id', $request->user()->id)
            ->where('writing_id', $writing->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
        } else {
            Favorite::query()->create([
                'user_id' => $request->user()->id,
                'writing_id' => $writing->id,
            ]);
        }

        return back();
    }

    public function comment(Request $request, Writing $writing): RedirectResponse
    {
        abort_unless($writing->status === 'published' && $writing->published_at !== null, 404);

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:2', 'max:2000'],
        ]);

        Comment::query()->create([
            'user_id' => $request->user()->id,
            'writing_id' => $writing->id,
            'content' => $validated['content'],
            'status' => 'pending',
        ]);

        return back()->with('status', 'Your comment was submitted for review.');
    }

    public function favorites(Request $request): RedirectResponse|View
    {
        $writings = $request->user()
            ->favorites()
            ->with(['writing.category:id,name,slug', 'writing.author:id,name'])
            ->latest()
            ->paginate(12);

        return view('library.favorites', compact('writings'));
    }

    public function history(Request $request): RedirectResponse|View
    {
        $histories = $request->user()
            ->readingHistories()
            ->with(['writing.category:id,name,slug', 'writing.author:id,name'])
            ->latest('last_read_at')
            ->paginate(12);

        return view('library.history', compact('histories'));
    }
}
