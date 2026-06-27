@extends('admin.layouts.app')
@section('title', 'Orders')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Orders</h4>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
                <input type="text" name="search" class="form-control form-control-sm" style="width:220px;"
                    placeholder="Search by customer..." value="{{ request('search') }}">
                <select name="status" class="form-select form-select-sm" style="width:160px;">
                    <option value="">All Statuses</option>
                    @foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-primary">Filter</button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td><span class="text-muted small">#{{ $order->id }}</span></td>
                            <td>
                                <div class="fw-semibold">{{ $order->user->name ?? '—' }}</div>
                                <div class="text-muted small">{{ $order->user->email ?? '' }}</div>
                            </td>
                            <td>${{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                @php
                                    $colors = [
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'shipped' => 'primary',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $colors[$order->status] ?? 'secondary' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="text-muted small">
                                {{ $order->created_at ? $order->created_at->format('M d, Y') : '—' }}
                            </td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" class="d-inline"
                                    onsubmit="return confirm('Delete this order?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $orders->links() }}</div>
@endsection
