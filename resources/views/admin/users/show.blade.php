@extends('admin.layouts.app')
@section('title', 'User Detail')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">User Detail</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row g-3">

    {{-- Profile Card --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-4">
            @if($user->avatar)
                <img src="{{ Storage::url($user->avatar) }}"
                     style="width:90px;height:90px;object-fit:cover;border-radius:50%;margin:0 auto 1rem;">
            @else
                <div style="width:90px;height:90px;border-radius:50%;background:#e0e7ff;
                            display:flex;align-items:center;justify-content:center;
                            font-weight:700;font-size:2rem;color:#6366f1;margin:0 auto 1rem;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif

            <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
            <p class="text-muted small mb-3">{{ $user->email }}</p>

            <div class="d-flex justify-content-center gap-3">
                <div class="text-center">
                    <div class="fw-bold">{{ $user->orders_count }}</div>
                    <div class="text-muted" style="font-size:0.75rem;">Orders</div>
                </div>
                <div class="text-center">
                    <div class="fw-bold">{{ $user->reviews_count }}</div>
                    <div class="text-muted" style="font-size:0.75rem;">Reviews</div>
                </div>
                <div class="text-center">
                    <div class="fw-bold">{{ $user->wishlists_count }}</div>
                    <div class="text-muted" style="font-size:0.75rem;">Wishlists</div>
                </div>
            </div>

            <hr>
            <div class="text-start small text-muted">
                <div class="mb-1">
                    <i class="bi bi-calendar me-1"></i>
                    Joined: {{ $user->created_at ? $user->created_at->format('M d, Y') : '—' }}
                </div>
                <div>
                    <i class="bi bi-shield me-1"></i>
                    Role: <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column --}}
    <div class="col-md-8 d-flex flex-column gap-3">

        {{-- Orders --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-cart3 me-1 text-warning"></i>
                    Orders ({{ $user->orders_count }} total)
                </h6>
            </div>
            <div class="card-body pt-2">
                @if($orders->isEmpty())
                    <p class="text-muted small mb-0">No orders yet.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small text-muted">
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                @php
                                    $colors = [
                                        'pending'    => 'warning',
                                        'processing' => 'info',
                                        'shipped'    => 'primary',
                                        'delivered'  => 'success',
                                        'cancelled'  => 'danger',
                                    ];
                                @endphp
                                <tr>
                                    <td class="small">#{{ $order->id }}</td>
                                    <td class="small text-muted">
                                        {{ $order->created_at ? $order->created_at->format('M d, Y') : '—' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $colors[$order->status] ?? 'secondary' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end small">${{ number_format($order->total_amount, 2) }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.orders.show', $order) }}"
                                           class="btn btn-xs btn-outline-primary"
                                           style="font-size:0.75rem;padding:2px 8px;">
                                            View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Reviews --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-star me-1 text-warning"></i>
                    Reviews ({{ $user->reviews_count }} total)
                </h6>
            </div>
            <div class="card-body pt-2">
                @if($reviews->isEmpty())
                    <p class="text-muted small mb-0">No reviews yet.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small text-muted">
                                    <th>Product</th>
                                    <th>Rating</th>
                                    <th>Comment</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reviews as $review)
                                <tr>
                                    <td class="small">{{ $review->product->name ?? '—' }}</td>
                                    <td>
                                        @for($i = 1; $i <= 5; $i++)
                                            <span style="color:{{ $i <= $review->rating ? '#f59e0b' : '#d1d5db' }};font-size:0.8rem;">★</span>
                                        @endfor
                                    </td>
                                    <td class="small text-muted">{{ Str::limit($review->comment, 50) }}</td>
                                    <td class="small text-muted">
                                        {{ $review->created_at ? $review->created_at->format('M d, Y') : '—' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Wishlists --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-heart me-1 text-danger"></i>
                    Wishlist ({{ $user->wishlists_count }} total)
                </h6>
            </div>
            <div class="card-body pt-2">
                @if($wishlists->isEmpty())
                    <p class="text-muted small mb-0">No wishlist items yet.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small text-muted">
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Added</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($wishlists as $item)
                                <tr>
                                    <td class="small">{{ $item->product->name ?? '—' }}</td>
                                    <td class="small">${{ number_format($item->product->price ?? 0, 2) }}</td>
                                    <td class="small text-muted">
                                        {{ $item->created_at ? $item->created_at->format('M d, Y') : '—' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection