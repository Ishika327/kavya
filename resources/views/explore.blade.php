@extends('layouts.app', ['title' => 'Explore Writings - Kavya'])

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
    <div>
        <p class="text-uppercase text-muted small mb-1">The library</p>
        <h1 class="h2 mb-0">Explore writings</h1>
    </div>
    <form class="d-flex gap-2" method="GET" action="{{ route('explore') }}">
        <label class="visually-hidden" for="search">Search writings</label>
        <input class="form-control" id="search" name="q" type="search" value="{{ $search }}" placeholder="Search writings">
        <button class="btn btn-dark" type="submit">Search</button>
    </form>
</div>
<form class="card border-0 shadow-sm mb-4" method="GET" action="{{ route('explore') }}">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-sm-6 col-lg-4">
                <label class="form-label small" for="category">Category</label>
                <select class="form-select" id="category" name="category">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected($categorySlug === $category->slug)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-sm-3 col-lg-2">
                <label class="form-label small" for="language">Language</label>
                <select class="form-select" id="language" name="language">
                    <option value="">All</option>
                    <option value="en" @selected($language === 'en')>English</option>
                    <option value="ne" @selected($language === 'ne')>Nepali</option>
                </select>
            </div>
            <div class="col-6 col-sm-3 col-lg-3">
                <label class="form-label small" for="sort">Sort by</label>
                <select class="form-select" id="sort" name="sort">
                    <option value="latest" @selected($sort === 'latest')>Latest</option>
                    <option value="popular" @selected($sort === 'popular')>Popular</option>
                </select>
            </div>
            @if ($search !== '')
                <input name="q" type="hidden" value="{{ $search }}">
            @endif
            <div class="col-12 col-lg-3 d-flex gap-2">
                <button class="btn btn-dark flex-grow-1" type="submit">Apply filters</button>
                <a class="btn btn-outline-secondary" href="{{ route('explore') }}">Reset</a>
            </div>
        </div>
    </div>
</form>
<div class="row g-3">
    @forelse ($writings as $writing)
        @include('partials.writing-card', ['writing' => $writing])
    @empty
        <div class="col-12"><div class="alert alert-light border">No published writings match your search.</div></div>
    @endforelse
</div>
<div class="mt-4">{{ $writings->links() }}</div>
@endsection
