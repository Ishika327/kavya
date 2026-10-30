<div class="col-12 col-md-6 col-xl-4">
    <article class="card writing-card shadow-sm h-100">
        @if ($writing->cover_image_path)
            <img class="card-img-top object-fit-cover" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($writing->cover_image_path) }}" alt="" height="160" loading="lazy">
        @endif
        <div class="card-body d-flex flex-column p-4">
            <p class="writing-category text-uppercase mb-2">{{ $writing->category->name }}</p>
            <h3 class="writing-title h5">{{ $writing->title }}</h3>
            <p class="text-muted small mb-3">{{ Str::limit($writing->content, 120) }}</p>
            <div class="d-flex justify-content-between align-items-center gap-2 mt-auto">
                <span class="small text-muted">{{ $writing->is_anonymous ? 'Anonymous' : ($writing->author?->name ?? 'Kavya') }}</span>
                <a class="btn btn-sm btn-outline-dark" href="{{ route('writings.show', $writing) }}">Read</a>
            </div>
        </div>
    </article>
</div>
