<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data">
            @csrf
            @if ($formMethod !== 'POST')
                @method($formMethod)
            @endif
            <div class="mb-3">
                <label class="form-label" for="name">Name</label>
                <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="{{ old('name', $category->name ?? '') }}" required autofocus>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label" for="description">Description <span class="text-muted">(optional)</span></label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $category->description ?? '') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label class="form-label" for="image">Category image <span class="text-muted">(optional)</span></label>
                <input class="form-control @error('image') is-invalid @enderror" id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp">
                <div class="form-text">JPG, PNG, or WebP up to 2 MB.</div>
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2">
                <button class="btn btn-dark" type="submit">{{ $submitLabel }}</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.categories.index') }}">Cancel</a>
            </div>
        </form>
    </div>
</div>
