@extends('admin.layouts.app')
@section('title', 'Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Products</h4>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Add Product
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if($product->image)
                            <img src="{{ Storage::url($product->image) }}"
                                 width="50" height="50"
                                 style="object-fit: cover; border-radius: 6px;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center"
                                 style="width:50px;height:50px;border-radius:6px;">
                                <i class="bi bi-image text-muted"></i>
                            </div>
                        @endif
                    </td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? '—' }}</td>
                    <td>${{ number_format($product->price, 2) }}</td>
                    <td>
                        <span class="{{ $product->stock < 10 ? 'text-danger fw-bold' : '' }}">
                            {{ $product->stock }}
                        </span>
                    </td>
                    <td>
                        @if($product->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.products.edit', $product) }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                              class="d-inline"
                              onsubmit="return confirm('Delete this product?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No products found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $products->links() }}</div>
@endsection