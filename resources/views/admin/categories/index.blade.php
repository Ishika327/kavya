@extends('layouts.app', ['title' => 'Categories - Kavya'])

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <p class="text-uppercase text-muted small mb-1">Content management</p>
        <h1 class="h2 mb-0">Categories</h1>
    </div>
    <a class="btn btn-dark" href="{{ route('admin.categories.create') }}">
        <i class="fa-solid fa-plus me-1" aria-hidden="true"></i>New category
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @forelse ($categories as $category)
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 p-3 p-md-4 border-bottom">
                <div class="d-flex align-items-center gap-3 min-w-0">
                    @if ($category->image_path)
                        <img class="rounded object-fit-cover" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($category->image_path) }}" alt="" width="56" height="56">
                    @else
                        <div class="rounded bg-light d-flex align-items-center justify-content-center text-muted" style="width: 56px; height: 56px;">
                            <i class="fa-solid fa-feather" aria-hidden="true"></i>
                        </div>
                    @endif
                    <div class="min-w-0">
                        <h2 class="h6 mb-1 text-truncate">{{ $category->name }}</h2>
                        <p class="text-muted small mb-0">{{ $category->writings_count }} {{ \Illuminate\Support\Str::plural('writing', $category->writings_count) }}</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.categories.edit', $category) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm" type="submit">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-4 text-muted">No categories have been created yet.</div>
        @endforelse
    </div>
</div>
@endsection
