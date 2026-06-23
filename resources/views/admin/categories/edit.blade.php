@extends('admin.layouts.app')
@section('title', 'Edit Category')

@section('content')
<div class="card border-0 shadow-sm" style="max-width: 600px;">
    <div class="card-body">
        <h5 class="card-title mb-4">Edit Category</h5>

        <form method="POST" action="{{ route('admin.categories.update', $category) }}">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $category->name) }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                          rows="3">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active"
                           id="is_active" value="1"
                           {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Update Category
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection