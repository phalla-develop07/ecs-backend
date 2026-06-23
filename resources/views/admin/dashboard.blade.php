@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="row g-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-1">🗂️</div>
            <h2 class="fw-bold">{{ $stats['total_categories'] }}</h2>
            <p class="text-muted mb-0">Categories</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-1">📦</div>
            <h2 class="fw-bold">{{ $stats['total_products'] }}</h2>
            <p class="text-muted mb-0">Products</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-1">👥</div>
            <h2 class="fw-bold">{{ $stats['total_users'] }}</h2>
            <p class="text-muted mb-0">Users</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-1">⚠️</div>
            <h2 class="fw-bold text-warning">{{ $stats['low_stock'] }}</h2>
            <p class="text-muted mb-0">Low Stock</p>
        </div>
    </div>
</div>
@endsection