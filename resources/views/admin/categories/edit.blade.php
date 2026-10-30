@extends('layouts.app', ['title' => 'Edit Category - Kavya'])

@section('content')
<div class="mb-4">
    <a class="text-decoration-none" href="{{ route('admin.categories.index') }}">
        <i class="fa-solid fa-arrow-left me-1" aria-hidden="true"></i>Back to categories
    </a>
    <h1 class="h2 mt-3 mb-0">Edit category</h1>
</div>
@include('admin.categories._form', ['formAction' => route('admin.categories.update', $category), 'formMethod' => 'PUT', 'submitLabel' => 'Save changes'])
@endsection
