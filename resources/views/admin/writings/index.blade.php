@extends('layouts.app', ['title' => 'Writings - Kavya'])

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <p class="text-uppercase text-muted small mb-1">Content management</p>
        <h1 class="h2 mb-0">Writings</h1>
    </div>
    <a class="btn btn-dark" href="{{ route('admin.writings.create') }}">
        <i class="fa-solid fa-plus me-1" aria-hidden="true"></i>New writing
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @forelse ($writings as $writing)
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 p-3 p-md-4 border-bottom">
                <div class="min-w-0">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h2 class="h6 mb-0 text-break">{{ $writing->title }}</h2>
                        @if ($writing->is_featured)
                            <span class="badge text-bg-warning">Featured</span>
                        @endif
                    </div>
                    <p class="text-muted small mb-0 mt-1">
                        {{ $writing->category->name }} · {{ $writing->author?->name ?? 'Anonymous' }}
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge rounded-pill text-bg-{{ $writing->status === 'published' ? 'success' : 'secondary' }}">{{ ucfirst($writing->status) }}</span>
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.writings.edit', $writing) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.writings.destroy', $writing) }}" onsubmit="return confirm('Delete this writing?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm" type="submit">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-4 text-muted">No writings have been created yet.</div>
        @endforelse
    </div>
    @if ($writings->hasPages())
        <div class="card-footer bg-white border-0 p-3">{{ $writings->links() }}</div>
    @endif
</div>
@endsection
