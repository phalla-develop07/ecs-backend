@extends('admin.layouts.app')
@section('title', 'Wishlists')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Wishlists</h4>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm" style="width:220px;"
                   placeholder="Search by user..." value="{{ request('search') }}">
            <button class="btn btn-sm btn-primary">Filter</button>
            <a href="{{ route('admin.wishlists.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Added</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($wishlists as $item)
                <tr>
                    <td class="text-muted small">{{ $loop->iteration }}</td>
                    <td>{{ $item->user->name ?? '—' }}</td>
                    <td>{{ $item->product->name ?? '—' }}</td>
                    <td>${{ number_format($item->product->price ?? 0, 2) }}</td>
                    <td class="text-muted small">{{ $item->created_at ? $item->created_at->format('M d, Y') : '—' }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.wishlists.destroy', $item) }}"
                              class="d-inline" onsubmit="return confirm('Remove this wishlist item?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No wishlist items found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $wishlists->links() }}</div>
@endsection