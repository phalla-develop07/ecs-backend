@extends('admin.layouts.app')
@section('title', 'Create Category')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="card border-0 shadow rounded-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h4 class="mb-0 fw-bold">
                        <i class="bi bi-folder-plus text-primary me-2"></i>
                        Create Category
                    </h4>
                    <small class="text-muted">
                        Fill in the information below to create a new category.
                    </small>
                </div>

                <div class="card-body p-4">

                    <form action="{{ route('admin.categories.store') }}"
                          method="POST"
                          enctype="multipart/form-data">

                        @csrf

                        <!-- Category Name -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Category Name
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}"
                                placeholder="Enter category name">

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Description
                            </label>

                            <textarea
                                name="description"
                                rows="4"
                                class="form-control @error('description') is-invalid @enderror"
                                placeholder="Write a short description...">{{ old('description') }}</textarea>

                            @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Category Image -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Category Image
                            </label>

                            <input
                                type="file"
                                name="image"
                                id="image"
                                accept="image/*"
                                class="form-control @error('image') is-invalid @enderror">

                            <small class="text-muted">
                                JPG, PNG, WEBP (Recommended size: 500 × 500 px)
                            </small>

                            @error('image')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <!-- Preview -->
                            <div class="mt-3">
                                <img
                                    id="preview"
                                    src="https://placehold.co/180x180?text=Preview"
                                    class="img-thumbnail rounded shadow-sm"
                                    style="max-width:180px;">
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold d-block">
                                Status
                            </label>

                            <div class="form-check form-switch">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="is_active"
                                    name="is_active"
                                    value="1"
                                    {{ old('is_active', true) ? 'checked' : '' }}>

                                <label class="form-check-label" for="is_active">
                                    Active Category
                                </label>
                            </div>
                        </div>

                        <hr>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-end gap-2">

                            <a href="{{ route('admin.categories.index') }}"
                               class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i>
                                Back
                            </a>

                            <button type="reset" class="btn btn-light border">
                                <i class="bi bi-arrow-clockwise"></i>
                                Reset
                            </button>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i>
                                Save Category
                            </button>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.getElementById('image').addEventListener('change', function(e) {

    const preview = document.getElementById('preview');
    const file = e.target.files[0];

    if(file){
        preview.src = URL.createObjectURL(file);
    }
});
</script>

@endsection