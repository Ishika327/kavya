@extends('layouts.app', ['title' => 'Reading History - Kavya'])

@section('content')
<div class="mb-4">
    <p class="text-uppercase text-muted small mb-1">Your reading journey</p>
    <h1 class="h2">Reading history</h1>
</div>
<div class="row g-3">
    @forelse ($histories as $history)
        @include('partials.writing-card', ['writing' => $history->writing])
    @empty
        <div class="col-12"><p class="text-muted">Your reading history will appear here as you explore.</p></div>
    @endforelse
</div>
<div class="mt-4">{{ $histories->links() }}</div>
@endsection
