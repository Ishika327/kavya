@extends('layouts.app', ['title' => $category->name.' - Kavya'])

@section('content')
<div class="mb-4">
    <a class="text-decoration-none" href="{{ route('categories') }}"><i class="fa-solid fa-arrow-left me-1" aria-hidden="true"></i>All categories</a>
    <p class="text-uppercase text-muted small mt-4 mb-1">Category</p>
    <h1 class="h2 mb-1">{{ $category->name }}</h1>
    <p class="text-muted">{{ $category->description ?? 'Writings gathered around '.$category->name.'.' }}</p>
</div>
<div class="row g-3">
    @forelse ($writings as $writing)
        @include('partials.writing-card', ['writing' => $writing])
    @empty
        <div class="col-12"><p class="text-muted">No published writings in this category yet.</p></div>
    @endforelse
</div>
<div class="mt-4">{{ $writings->links() }}</div>
@endsection
