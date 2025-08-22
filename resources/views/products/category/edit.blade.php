@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h5>Edit Category</h5>
    </div>
    <div class="card-body">
        @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}

    </div>
@endif

            <form action="{{ route('categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
    <label for="category_name" class="form-label">Category Name</label>
    <input type="text" name="name" id="category_name" class="form-control" value="{{ old('name', $category->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="category_code" class="form-label">Category Code</label>
    <input type="text" name="code" id="category_code" class="form-control" value="{{ old('code', $category->category_code ?? '') }}" required>
</div>


            <button type="submit" class="btn btn-pink">Update</button>
          <a href="{{ route('categories.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>
@endsection
