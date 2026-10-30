<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data">
            @csrf
            @if ($formMethod !== 'POST')
                @method($formMethod)
            @endif
            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <label class="form-label" for="title">Title</label>
                    <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" type="text" value="{{ old('title', $writing->title ?? '') }}" required autofocus>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-lg-4">
                    <label class="form-label" for="category_id">Category</label>
                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                        <option value="">Select category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) old('category_id', $writing->category_id ?? '') === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label" for="content">Content</label>
                    <textarea class="form-control font-monospace @error('content') is-invalid @enderror" id="content" name="content" rows="14" required>{{ old('content', $writing->content ?? '') }}</textarea>
                    <div class="form-text">Line breaks and spacing are preserved when the writing is displayed.</div>
                    @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label" for="language">Language</label>
                    <select class="form-select @error('language') is-invalid @enderror" id="language" name="language" required>
                        <option value="en" @selected(old('language', $writing->language ?? 'en') === 'en')>English</option>
                        <option value="ne" @selected(old('language', $writing->language ?? 'en') === 'ne')>Nepali</option>
                    </select>
                    @error('language')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label" for="status">Status</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="draft" @selected(old('status', $writing->status ?? 'draft') === 'draft')>Draft</option>
                        <option value="published" @selected(old('status', $writing->status ?? 'draft') === 'published')>Published</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label" for="cover_image">Cover image <span class="text-muted">(optional)</span></label>
                    <input class="form-control @error('cover_image') is-invalid @enderror" id="cover_image" name="cover_image" type="file" accept=".jpg,.jpeg,.png,.webp">
                    <div class="form-text">JPG, PNG, or WebP up to 4 MB.</div>
                    @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <div class="d-flex flex-column gap-2">
                        @foreach ([
                            'is_featured' => 'Feature this writing',
                            'is_poem_of_day' => 'Poem of the day',
                            'is_poem_of_week' => 'Poem of the week',
                            'is_anonymous' => 'Publish anonymously',
                        ] as $field => $label)
                            <div class="form-check">
                                <input class="form-check-input" id="{{ $field }}" name="{{ $field }}" type="checkbox" value="1" @checked(old($field, $writing->{$field} ?? false))>
                                <label class="form-check-label" for="{{ $field }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                <button class="btn btn-dark" type="submit">{{ $submitLabel }}</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.writings.index') }}">Cancel</a>
            </div>
        </form>
    </div>
</div>
