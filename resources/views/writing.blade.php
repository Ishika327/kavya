@extends('layouts.app', ['title' => $writing->title.' - Kavya'])

@section('content')
@php
    $previousUrl = url()->previous();
    $previousOrigin = parse_url($previousUrl, PHP_URL_SCHEME).'://'.parse_url($previousUrl, PHP_URL_HOST).':'.parse_url($previousUrl, PHP_URL_PORT);
    $currentOrigin = request()->getScheme().'://'.request()->getHost().':'.request()->getPort();
    $backUrl = $previousOrigin === $currentOrigin ? $previousUrl : route('explore');
@endphp
<article class="mx-auto" id="reading-page" style="max-width: 760px;" data-title="{{ $writing->title }}" data-content="{{ $writing->content }}" data-category="{{ $writing->category->name }}">
    <a class="text-decoration-none" href="{{ $backUrl !== url()->current() ? $backUrl : route('explore') }}">
        <i class="fa-solid fa-arrow-left me-1" aria-hidden="true"></i>Back to writings
    </a>
    <header class="mt-4 mb-4">
        <p class="text-uppercase text-muted small mb-2">{{ $writing->category->name }}</p>
        <h1 class="display-5 fw-semibold">{{ $writing->title }}</h1>
        <p class="text-muted mb-0">
            {{ $writing->is_anonymous ? 'Anonymous' : ($writing->author?->name ?? 'Kavya') }}
            · {{ $writing->published_at->format('M j, Y') }}
            · {{ number_format($writing->view_count) }} views
        </p>
    </header>
    @if ($writing->cover_image_path)
        <img class="img-fluid rounded-4 shadow-sm mb-4 w-100 object-fit-cover" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($writing->cover_image_path) }}" alt="" style="max-height: 360px;" loading="lazy">
    @endif
    <div class="card reading-shell shadow-sm">
        <div class="card-body p-4 p-md-5">
            <div class="reading-content lh-lg" style="white-space: pre-wrap;">{{ $writing->content }}</div>
        </div>
    </div>
    <div class="d-flex flex-wrap gap-2 mt-4 align-items-center">
        <button class="btn btn-outline-dark" id="reading-mode-toggle" type="button">
            <i class="fa-solid fa-expand me-1" aria-hidden="true"></i>Reading mode
        </button>
        <button class="btn btn-outline-dark" id="reading-size-toggle" type="button">
            <i class="fa-solid fa-text-height me-1" aria-hidden="true"></i>Text size
        </button>
        @auth
            <form method="POST" action="{{ route('writings.like', $writing) }}">
                @csrf
                <button class="btn {{ $isLiked ? 'btn-dark' : 'btn-outline-dark' }}" type="submit">
                    <i class="fa-{{ $isLiked ? 'solid' : 'regular' }} fa-heart me-1" aria-hidden="true"></i>{{ $isLiked ? 'Liked' : 'Like' }} · {{ number_format($writing->like_count) }}
                </button>
            </form>
            <form method="POST" action="{{ route('writings.favorite', $writing) }}">
                @csrf
                <button class="btn {{ $isFavorited ? 'btn-dark' : 'btn-outline-dark' }}" type="submit">
                    <i class="fa-{{ $isFavorited ? 'solid' : 'regular' }} fa-bookmark me-1" aria-hidden="true"></i>{{ $isFavorited ? 'Saved' : 'Save' }}
                </button>
            </form>
        @else
            <a class="btn btn-outline-dark" href="{{ route('login') }}">Login to like or save</a>
        @endauth
        <button class="btn btn-outline-dark" id="share-writing" type="button">
            <i class="fa-solid fa-share-nodes me-1" aria-hidden="true"></i>Share
        </button>
        <button class="btn btn-outline-dark" id="share-writing-image" type="button">
            <i class="fa-solid fa-image me-1" aria-hidden="true"></i>Share as image
        </button>
    </div>
    @auth
        <section class="mt-5">
            <h2 class="h4 mb-3">Leave a thought</h2>
            <form method="POST" action="{{ route('writings.comments.store', $writing) }}">
                @csrf
                <label class="visually-hidden" for="comment-content">Comment</label>
                <textarea class="form-control @error('content') is-invalid @enderror" id="comment-content" name="content" rows="4" placeholder="Share your thoughts..." required>{{ old('content') }}</textarea>
                @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <button class="btn btn-dark mt-2" type="submit">Submit comment</button>
            </form>
        </section>
    @endauth
    @if ($writing->comments->isNotEmpty())
        <section class="mt-5">
            <h2 class="h4 mb-3">Reader thoughts</h2>
            @foreach ($writing->comments as $comment)
                <div class="border-bottom py-3">
                    <p class="fw-semibold mb-1">{{ $comment->user->name }}</p>
                    <p class="mb-0 text-muted">{{ $comment->content }}</p>
                </div>
            @endforeach
        </section>
    @endif
    @if ($relatedWritings->isNotEmpty())
        <section class="mt-5">
            <h2 class="h4 mb-3">More from {{ $writing->category->name }}</h2>
            <div class="row g-3">
                @foreach ($relatedWritings as $relatedWriting)
                    @include('partials.writing-card', ['writing' => $relatedWriting])
                @endforeach
            </div>
        </section>
    @endif
</article>
@pushOnce('scripts')
    <script>
        (() => {
            const page = document.getElementById('reading-page');
            const readingModeToggle = document.getElementById('reading-mode-toggle');
            const readingSizeToggle = document.getElementById('reading-size-toggle');
            const shareButton = document.getElementById('share-writing');
            const imageButton = document.getElementById('share-writing-image');

            readingModeToggle?.addEventListener('click', () => {
                document.body.classList.toggle('reading-mode-active');
                readingModeToggle.innerHTML = document.body.classList.contains('reading-mode-active')
                    ? '<i class="fa-solid fa-compress me-1" aria-hidden="true"></i>Exit reading mode'
                    : '<i class="fa-solid fa-expand me-1" aria-hidden="true"></i>Reading mode';
            });

            readingSizeToggle?.addEventListener('click', () => {
                page.classList.toggle('reading-size-large');
            });

            shareButton?.addEventListener('click', async () => {
                const shareData = { title: page.dataset.title, url: window.location.href };
                if (navigator.share) {
                    await navigator.share(shareData);
                    return;
                }

                await navigator.clipboard.writeText(window.location.href);
                shareButton.innerHTML = '<i class="fa-solid fa-check me-1" aria-hidden="true"></i>Link copied';
            });

            imageButton?.addEventListener('click', async () => {
                const canvas = document.createElement('canvas');
                canvas.width = 1080;
                canvas.height = 1350;
                const context = canvas.getContext('2d');
                const padding = 100;
                const lines = wrapText(context, page.dataset.content, 880, 36);

                context.fillStyle = '#f7f3ed';
                context.fillRect(0, 0, canvas.width, canvas.height);
                context.fillStyle = '#2b2521';
                context.font = 'bold 54px Georgia';
                context.fillText(page.dataset.title, padding, 190, 880);
                context.fillStyle = '#9c5d43';
                context.font = 'bold 24px Arial';
                context.fillText(page.dataset.category.toUpperCase(), padding, 120);
                context.fillStyle = '#382f29';
                context.font = '32px Georgia';
                lines.slice(0, 24).forEach((line, index) => {
                    context.fillText(line, padding, 310 + (index * 46));
                });
                context.fillStyle = '#746b63';
                context.font = 'bold 26px Georgia';
                context.fillText('KAVYA', padding, 1250);

                canvas.toBlob(async (blob) => {
                    if (!blob) {
                        return;
                    }
                    const file = new File([blob], 'kavya-writing.png', { type: 'image/png' });
                    if (navigator.share && navigator.canShare?.({ files: [file] })) {
                        await navigator.share({ title: page.dataset.title, files: [file] });
                        return;
                    }
                    const link = document.createElement('a');
                    link.download = 'kavya-writing.png';
                    link.href = URL.createObjectURL(blob);
                    link.click();
                    URL.revokeObjectURL(link.href);
                }, 'image/png');
            });

            function wrapText(context, text, maxWidth, fontSize) {
                context.font = `${fontSize}px Georgia`;
                return text.split('\n').flatMap((paragraph) => {
                    const words = paragraph.split(' ');
                    const lines = [];
                    let line = '';
                    words.forEach((word) => {
                        const candidate = line ? `${line} ${word}` : word;
                        if (context.measureText(candidate).width > maxWidth && line) {
                            lines.push(line);
                            line = word;
                        } else {
                            line = candidate;
                        }
                    });
                    lines.push(line);
                    return lines;
                });
            }
        })();
    </script>
@endPushOnce
@endsection
