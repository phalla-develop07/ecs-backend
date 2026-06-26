@extends('admin.layouts.app')
@section('title', 'Edit Category')

@section('content')
<div class="card border-0 shadow-sm" style="max-width: 600px;">
    <div class="card-body">
        <h5 class="card-title mb-4">Edit Category</h5>

        {{-- ✅ enctype required for file uploads --}}
        <form method="POST" action="{{ route('admin.categories.update', $category) }}"
              enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                <input type="text" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $category->name) }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description"
                          class="form-control @error('description') is-invalid @enderror"
                          rows="3">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- ✅ Image upload with current image preview --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Image</label>

                {{-- Show current image if exists --}}
                @if($category->image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $category->image) }}"
                             alt="{{ $category->name }}"
                             width="100" height="100"
                             class="rounded object-fit-cover border">
                        <p class="text-muted small mt-1 mb-0">Current image — upload a new one to replace it</p>
                    </div>
                @endif

                <input type="file" name="image" accept="image/*"
                       class="form-control @error('image') is-invalid @enderror"
                       id="imageInput">
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                {{-- Live preview before submitting --}}
                <div id="imagePreview" class="mt-2 d-none">
                    <img id="previewImg" src="" alt="Preview"
                         width="100" height="100"
                         class="rounded object-fit-cover border">
                    <p class="text-muted small mt-1 mb-0">New image preview</p>
                </div>
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

{{-- Live image preview script --}}
<script>
document.getElementById('imageInput').addEventListener('change', function () {
    const file = this.files[0];
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');

    if (file) {
        previewImg.src = URL.createObjectURL(file);
        preview.classList.remove('d-none');
    } else {
        preview.classList.add('d-none');
    }
});
</script>
@endsection