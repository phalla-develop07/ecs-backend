{{-- resources/views/admin/dashboard.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Welcome back, {{ auth()->user()->name }} 👋</h4>
        <p class="text-muted small mb-0">{{ now()->format('l, F j, Y') }}</p>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">

    <div class="col-md-4 col-xl-2">
        <a href="{{ route('admin.categories.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-left: 4px solid #6366f1 !important;">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon rounded-3 d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#ede9fe;">
                        <span style="font-size:1.4rem;">🗂️</span>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">{{ $stats['total_categories'] }}</div>
                        <div class="text-muted small">Categories</div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4 col-xl-2">
        <a href="{{ route('admin.products.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-left: 4px solid #0ea5e9 !important;">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon rounded-3 d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#e0f2fe;">
                        <span style="font-size:1.4rem;">📦</span>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">{{ $stats['total_products'] }}</div>
                        <div class="text-muted small">Products</div>
                        @if($stats['low_stock'] > 0)
                            <span class="badge bg-warning text-dark" style="font-size:0.65rem;">
                                {{ $stats['low_stock'] }} low stock
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4 col-xl-2">
        <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-left: 4px solid #10b981 !important;">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon rounded-3 d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#d1fae5;">
                        <span style="font-size:1.4rem;">👥</span>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">{{ $stats['total_users'] }}</div>
                        <div class="text-muted small">Users</div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4 col-xl-2">
        <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-left: 4px solid #f59e0b !important;">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon rounded-3 d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#fef3c7;">
                        <span style="font-size:1.4rem;">🛒</span>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">{{ $stats['total_orders'] }}</div>
                        <div class="text-muted small">Orders</div>
                        @if($stats['pending_orders'] > 0)
                            <span class="badge bg-danger" style="font-size:0.65rem;">
                                {{ $stats['pending_orders'] }} pending
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4 col-xl-2">
        <a href="{{ route('admin.reviews.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-left: 4px solid #ec4899 !important;">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon rounded-3 d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#fce7f3;">
                        <span style="font-size:1.4rem;">⭐</span>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">{{ $stats['total_reviews'] }}</div>
                        <div class="text-muted small">Reviews</div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4 col-xl-2">
        <a href="{{ route('admin.wishlists.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-left: 4px solid #ef4444 !important;">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon rounded-3 d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#fee2e2;">
                        <span style="font-size:1.4rem;">❤️</span>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">{{ $stats['total_wishlists'] }}</div>
                        <div class="text-muted small">Wishlists</div>
                    </div>
                </div>
            </div>
        </a>
    </div>

</div>

{{-- Quick Actions --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h6 class="fw-semibold mb-3 text-muted text-uppercase" style="font-size:0.75rem;letter-spacing:.08em;">
            Quick Actions
        </h6>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-plus me-1"></i> New Category
            </a>
            <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-plus me-1"></i> New Product
            </a>
            <a href="{{ route('admin.orders.index') }}?status=pending" class="btn btn-sm btn-outline-warning">
                <i class="bi bi-clock me-1"></i> Pending Orders
            </a>
            <a href="{{ route('admin.reviews.index') }}" class="btn btn-sm btn-outline-pink">
                <i class="bi bi-star me-1"></i> Manage Reviews
            </a>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .stat-card {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        border-radius: 12px;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.10) !important;
    }
</style>
@endpush