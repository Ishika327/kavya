<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreWritingRequest;
use App\Http\Requests\Admin\UpdateWritingRequest;
use App\Models\Category;
use App\Models\Writing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WritingController extends Controller
{
    public function index(): View
    {
        $writings = Writing::query()
            ->select(['id', 'category_id', 'author_id', 'title', 'status', 'is_featured', 'published_at', 'created_at'])
            ->with(['category:id,name', 'author:id,name'])
            ->latest()
            ->latest('id')
            ->paginate(12);

        return view('admin.writings.index', compact('writings'));
    }

    public function create(): View
    {
        $categories = Category::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.writings.create', compact('categories'));
    }

    public function store(StoreWritingRequest $request): RedirectResponse
    {
        $data = $request->safe()->only([
            'category_id',
            'title',
            'slug',
            'content',
            'language',
            'status',
            'is_featured',
            'is_poem_of_day',
            'is_poem_of_week',
            'is_anonymous',
        ]);
        $data['author_id'] = $request->user()->id;
        $data['published_at'] = $data['status'] === 'published' ? now() : null;

        if ($request->hasFile('cover_image')) {
            $data['cover_image_path'] = $request->file('cover_image')->store('writings', 'public');
        }

        DB::transaction(function () use ($data): void {
            $writing = Writing::query()->create($data);
            $this->clearExclusiveSelections($writing);
        });

        return redirect()
            ->route('admin.writings.index')
            ->with('status', 'Writing created successfully.');
    }

    public function edit(Writing $writing): View
    {
        $categories = Category::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.writings.edit', compact('writing', 'categories'));
    }

    public function update(UpdateWritingRequest $request, Writing $writing): RedirectResponse
    {
        $data = $request->safe()->only([
            'category_id',
            'title',
            'slug',
            'content',
            'language',
            'status',
            'is_featured',
            'is_poem_of_day',
            'is_poem_of_week',
            'is_anonymous',
        ]);
        $wasPublished = $writing->status === 'published';
        $data['published_at'] = $data['status'] === 'published'
            ? ($wasPublished ? $writing->published_at : now())
            : null;

        $oldImage = $writing->cover_image_path;
        if ($request->hasFile('cover_image')) {
            $data['cover_image_path'] = $request->file('cover_image')->store('writings', 'public');
        }

        DB::transaction(function () use ($data, $writing): void {
            $writing->update($data);
            $this->clearExclusiveSelections($writing);
        });

        if ($request->hasFile('cover_image') && $oldImage !== null) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()
            ->route('admin.writings.index')
            ->with('status', 'Writing updated successfully.');
    }

    public function destroy(Writing $writing): RedirectResponse
    {
        $imagePath = $writing->cover_image_path;
        $writing->delete();

        if ($imagePath !== null) {
            Storage::disk('public')->delete($imagePath);
        }

        return redirect()
            ->route('admin.writings.index')
            ->with('status', 'Writing deleted successfully.');
    }

    private function clearExclusiveSelections(Writing $writing): void
    {
        if ($writing->is_poem_of_day) {
            Writing::query()
                ->whereKeyNot($writing->id)
                ->where('is_poem_of_day', true)
                ->update(['is_poem_of_day' => false]);
        }

        if ($writing->is_poem_of_week) {
            Writing::query()
                ->whereKeyNot($writing->id)
                ->where('is_poem_of_week', true)
                ->update(['is_poem_of_week' => false]);
        }
    }
}
