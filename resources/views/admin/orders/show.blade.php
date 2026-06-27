@extends('admin.layouts.app')
@section('title', 'Order #{{ $order->id }}')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Order <span class="text-muted">#{{ $order->id }}</span></h4>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row g-3">
    {{-- Order Info --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Customer</h6>
                <p class="mb-1 fw-semibold">{{ $order->user->name }}</p>
                <p class="mb-0 text-muted small">{{ $order->user->email }}</p>

                <hr>
                <h6 class="fw-semibold mb-3">Update Status</h6>
                <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                    @csrf @method('PUT')
                    <select name="status" class="form-select form-select-sm mb-2">
                        @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
                            <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                    <button class="btn btn-primary btn-sm w-100">Update Status</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Order Items --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product->name ?? 'Deleted Product' }}</td>
                            <td>${{ number_format($item->price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total</td>
                            <td class="fw-bold">${{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection