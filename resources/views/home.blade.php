@extends('layouts.app', ['title' => 'Kavya - Every Word Has a Feeling'])

@section('content')
<section class="kavya-hero rounded-4 text-white p-4 p-md-5 mb-5">
    <div class="row align-items-center g-4">
        <div class="col-12 col-lg-7">
            <p class="text-uppercase small opacity-75 mb-2">A home for feeling</p>
            <h1 class="display-4 fw-semibold">Every Word Has a Feeling.</h1>
            <p class="lead opacity-75 mb-4">Discover poems, stories, and quiet thoughts written for the moments that stay with you.</p>
            <form class="row g-2" method="GET" action="{{ route('explore') }}">
                <div class="col-12 col-sm">
                    <label class="visually-hidden" for="home-search">Search writings</label>
                    <input class="form-control form-control-lg" id="home-search" name="q" type="search" placeholder="Search a feeling, title, or word">
                </div>
                <div class="col-12 col-sm-auto">
                    <button class="btn btn-light btn-lg w-100" type="submit"><i class="fa-solid fa-magnifying-glass me-1" aria-hidden="true"></i>Explore</button>
                </div>
            </form>
        </div>
        <div class="col-lg-5 d-none d-lg-block text-center">
            <i class="fa-solid fa-feather-pointed display-1 opacity-25" aria-hidden="true"></i>
        </div>f
    </div>
</section>

@if ($featuredWritings->isNotEmpty())
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="section-heading h3 mb-0">Featured writings</h2>
            <a class="small text-decoration-none" href="{{ route('explore') }}">See all <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
        <div class="row g-3">
            @foreach ($featuredWritings as $writing)
                @include('partials.writing-card', ['writing' => $writing])
            @endforeach
        </div>
    </section>
@endif

<section class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="section-heading h3 mb-0">Browse by category</h2>
        <a class="small text-decoration-none" href="{{ route('categories') }}">All categories <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
    </div>
    <div class="d-flex gap-2 overflow-auto pb-2">
        @foreach ($categories as $category)
            <a class="btn kavya-chip rounded-pill text-nowrap" href="{{ route('categories.show', $category) }}">{{ $category->name }} <span class="text-muted">({{ $category->writings_count }})</span></a>
        @endforeach
    </div>
</section>

@foreach ([
    ['title' => 'Latest writings', 'items' => $latestWritings],
    ['title' => 'Popular right now', 'items' => $popularWritings],
] as $section)
    <section class="mb-5">
        <h2 class="section-heading h3 mb-3">{{ $section['title'] }}</h2>
        <div class="row g-3">
            @forelse ($section['items'] as $writing)
                @include('partials.writing-card', ['writing' => $writing])
            @empty
                <div class="col-12"><p class="text-muted">Published writings will appear here soon.</p></div>
            @endforelse
        </div>
    </section>
@endforeach

@if ($poemOfDay || $poemOfWeek)
    <section class="row g-3 mb-4">
        @foreach ([$poemOfDay, $poemOfWeek] as $special)
            @if ($special)
                <div class="col-12 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <p class="text-uppercase text-muted small mb-2">{{ $special === $poemOfDay ? 'Poem of the day' : 'Poem of the week' }}</p>
                            <h2 class="literary-heading h4">{{ $special->title }}</h2>
                            <p class="text-muted mb-3">{{ Str::limit($special->content, 160) }}</p>
                            <a class="btn btn-outline-dark btn-sm" href="{{ route('writings.show', $special) }}">Read writing</a>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </section>
@endif
@endsection
