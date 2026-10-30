@extends('layouts.app', ['title' => 'Edit Writing - Kavya'])

@section('content')
<div class="mb-4">
    <a class="text-decoration-none" href="{{ route('admin.writings.index') }}">
        <i class="fa-solid fa-arrow-left me-1" aria-hidden="true"></i>Back to writings
    </a>
    <h1 class="h2 mt-3 mb-0">Edit writing</h1>
</div>
@include('admin.writings._form', ['formAction' => route('admin.writings.update', $writing), 'formMethod' => 'PUT', 'submitLabel' => 'Save changes'])
@endsection
