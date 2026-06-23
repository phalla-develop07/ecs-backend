@extends('admin.layouts.app')
@section('title', 'Create Product')

@section('content')
<div class="card border-0 shadow-sm" style="max-width: 700px;">
    <div class="card-body">
        <h5 class="card-title mb-4">Create Product</h5>

        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="e.g. iPhone 15 Pro">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Price ($) <span class="text-danger">*</span></label>
                    <input type="number" name="price" step="0.01" min="0"
                           class="form-control @error('price') is-invalid @enderror"
                           value="{{ old('price') }}" placeholder="0.00">
                    @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Stock <span class="text-danger">*</span></label>
                    <input type="number" name="stock" min="0"
                           class="form-control @error('stock') is-invalid @enderror"
                           value="{{ old('stock', 0) }}">
                    @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" rows="4"
                              class="form-control @error('description') is-invalid @enderror"
                              placeholder="Product description...">{{ old('description') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Product Image</label>
                    <input type="file" name="image" accept="image/*"
                           class="form-control @error('image') is-invalid @enderror"
                           onchange="previewImage(this)">
                    <div class="form-text">JPG, PNG, WEBP — max 2MB</div>
                    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <img id="preview" src="#" alt="Preview"
                         class="mt-2 d-none rounded"
                         style="max-height: 160px; object-fit: cover;">
                </div>

                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active"
                               id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Save Product
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    const preview = document.getElementById('preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush