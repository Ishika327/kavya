@extends('layouts.app', ['title' => 'Admin Dashboard - Kavya'])

@section('content')
<div class="d-flex flex-column flex-lg-row gap-4">
    <aside class="offcanvas-lg offcanvas-start flex-shrink-0" id="adminSidebar" tabindex="-1" aria-labelledby="adminSidebarLabel" style="width: 250px;">
        <div class="offcanvas-header border-bottom">
            <h2 class="h5 mb-0" id="adminSidebarLabel">Kavya admin</h2>
            <button class="btn-close d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#adminSidebar" type="button" aria-label="Close menu"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column p-3 p-lg-0">
            <nav class="nav nav-pills flex-column gap-1">
                <a class="nav-link active" href="{{ route('admin.dashboard') }}">
                    <i class="fa-solid fa-chart-line me-2" aria-hidden="true"></i>Overview
                </a>
                <a class="nav-link" href="{{ route('admin.writings.index') }}">
                    <i class="fa-solid fa-feather me-2" aria-hidden="true"></i>Writings
                </a>
                <a class="nav-link" href="{{ route('admin.categories.index') }}">
                    <i class="fa-solid fa-tags me-2" aria-hidden="true"></i>Categories
                </a>
                <span class="nav-link text-muted">
                    <i class="fa-solid fa-users me-2" aria-hidden="true"></i>Users <small>(next phase)</small>
                </span>
            </nav>
        </div>
    </aside>

    <section class="flex-grow-1 min-w-0">
        <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
            <div>
                <p class="text-uppercase text-muted small mb-1">Kavya administration</p>
                <h1 class="h2 mb-0">Admin dashboard</h1>
            </div>
            <button class="btn btn-dark d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" type="button" aria-controls="adminSidebar">
                <i class="fa-solid fa-bars me-1" aria-hidden="true"></i>Menu
            </button>
        </div>

        <div class="row g-3">
            @foreach ([
                ['label' => 'Total writings', 'value' => $statistics['writings'], 'icon' => 'fa-feather', 'tone' => 'primary'],
                ['label' => 'Published', 'value' => $statistics['publishedWritings'], 'icon' => 'fa-circle-check', 'tone' => 'success'],
                ['label' => 'Categories', 'value' => $statistics['categories'], 'icon' => 'fa-tags', 'tone' => 'warning'],
                ['label' => 'Readers', 'value' => $statistics['users'], 'icon' => 'fa-users', 'tone' => 'info'],
                ['label' => 'Likes', 'value' => $statistics['likes'], 'icon' => 'fa-heart', 'tone' => 'danger'],
                ['label' => 'Views', 'value' => $statistics['views'], 'icon' => 'fa-eye', 'tone' => 'secondary'],
            ] as $statistic)
                <div class="col-6 col-xl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-2">
                                <span class="text-muted small">{{ $statistic['label'] }}</span>
                                <span class="text-{{ $statistic['tone'] }}"><i class="fa-solid {{ $statistic['icon'] }}" aria-hidden="true"></i></span>
                            </div>
                            <p class="display-6 fw-semibold mb-0 mt-2">{{ number_format($statistic['value']) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-4 mt-1">
            <div class="col-12 col-xl-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h2 class="h5 mb-0">Recent writings</h2>
                    </div>
                    <div class="card-body px-4">
                        @forelse ($recentWritings as $writing)
                            <div class="d-flex align-items-start justify-content-between gap-3 py-3 border-bottom">
                                <div class="min-w-0">
                                    <h3 class="h6 text-truncate mb-1">{{ $writing->title }}</h3>
                                    <p class="text-muted small mb-0">
                                        {{ $writing->category->name }} · {{ $writing->author?->name ?? 'Anonymous' }}
                                    </p>
                                </div>
                                <span class="badge rounded-pill text-bg-{{ $writing->status === 'published' ? 'success' : 'secondary' }}">{{ ucfirst($writing->status) }}</span>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No writings have been created yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h2 class="h5 mb-0">Recent readers</h2>
                    </div>
                    <div class="card-body px-4">
                        @forelse ($recentUsers as $user)
                            <div class="d-flex align-items-center justify-content-between gap-3 py-3 border-bottom">
                                <div class="min-w-0">
                                    <h3 class="h6 text-truncate mb-1">{{ $user->name }}</h3>
                                    <p class="text-muted small text-truncate mb-0">{{ $user->email }}</p>
                                </div>
                                <span class="badge text-bg-light">{{ ucfirst($user->role) }}</span>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No readers have registered yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h2 class="h5 mb-0">Recent comments</h2>
            </div>
            <div class="card-body px-4">
                @forelse ($recentComments as $comment)
                    <div class="py-3 border-bottom">
                        <div class="d-flex justify-content-between gap-3">
                            <p class="fw-semibold mb-1">{{ $comment->user->name }} on {{ $comment->writing->title }}</p>
                            <span class="badge text-bg-{{ $comment->status === 'approved' ? 'success' : 'warning' }}">{{ ucfirst($comment->status) }}</span>
                        </div>
                        <p class="text-muted mb-0">{{ $comment->content }}</p>
                    </div>
                @empty
                    <p class="text-muted mb-0">No comments have been submitted yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@pushOnce('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
@endPushOnce
@endsection
