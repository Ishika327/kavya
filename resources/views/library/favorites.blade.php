@extends('layouts.app', ['title' => 'My Favorites - Kavya'])

@section('content')
<div class="mb-4">
    <p class="text-uppercase text-muted small mb-1">Your library</p>
    <h1 class="h2">My favorites</h1>
</div>
<div class="row g-3">
    @forelse ($writings as $favorite)
        @include('partials.writing-card', ['writing' => $favorite->writing])
    @empty
        <div class="col-12"><p class="text-muted">You have not saved any writings yet.</p></div>
    @endforelse
</div>
<div class="mt-4">{{ $writings->links() }}</div>
@endsection
