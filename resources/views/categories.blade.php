@extends('layouts.app', ['title' => 'Categories - Kavya'])

@section('content')
<div class="mb-4">
    <p class="text-uppercase text-muted small mb-1">Find your feeling</p>
    <h1 class="h2">Browse categories</h1>
</div>
<div class="row g-3">
    @foreach ($categories as $category)
        <div class="col-12 col-sm-6 col-lg-4">
            <a class="card border-0 shadow-sm h-100 text-decoration-none text-dark" href="{{ route('categories.show', $category) }}">
                <div class="card-body p-4">
                    <i class="fa-solid fa-feather text-muted mb-3" aria-hidden="true"></i>
                    <h2 class="h5">{{ $category->name }}</h2>
                    <p class="text-muted small mb-2">{{ $category->description ?? 'Explore writings in this collection.' }}</p>
                    <span class="small">{{ $category->writings_count }} published {{ Str::plural('writing', $category->writings_count) }} <i class="fa-solid fa-arrow-right ms-1" aria-hidden="true"></i></span>
                </div>
            </a>
        </div>
    @endforeach
</div>
@endsection
