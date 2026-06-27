@extends('admin.layouts.app')
@section('title', 'Reviews')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Reviews</h4>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
                <input type="text" name="search" class="form-control form-control-sm" style="width:220px;"
                    placeholder="Search comment or user..." value="{{ request('search') }}">
                <select name="rating" class="form-select form-select-sm" style="width:130px;">
                    <option value="">All Ratings</option>
                    @for ($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                            {{ $i }} ⭐
                        </option>
                    @endfor
                </select>
                <button class="btn btn-sm btn-primary">Filter</button>
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
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
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                        <tr>
                            <td class="text-muted small">{{ $loop->iteration }}</td>
                            <td>{{ $review->user->name ?? '—' }}</td>
                            <td>{{ $review->product->name ?? '—' }}</td>
                            <td>
                                @for ($i = 1; $i <= 5; $i++)
                                    <span style="color: {{ $i <= $review->rating ? '#f59e0b' : '#d1d5db' }};">★</span>
                                @endfor
                            </td>
                            <td class="text-muted small">{{ Str::limit($review->comment, 60) }}</td>
                            <td class="text-muted small">
                                {{ $review->created_at ? $review->created_at->format('M d, Y') : '—' }}
                            </td>
                            <td>
                                <a href="{{ route('admin.reviews.show', $review) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}"
                                    class="d-inline" onsubmit="return confirm('Delete this review?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No reviews found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $reviews->links() }}</div>
@endsection
