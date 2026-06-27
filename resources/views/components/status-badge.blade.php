@props(['status'])
@php
    $map = [
        'pending'     => 'warning text-dark',
        'processing'  => 'info text-dark',
        'shipped'     => 'primary',
        'delivered'   => 'success',
        'completed'   => 'success',
        'cancelled'   => 'danger',
        'canceled'    => 'danger',
    ];
    $class = $map[strtolower($status ?? '')] ?? 'secondary';
@endphp
<span class="badge bg-{{ $class }} text-uppercase fw-semibold">{{ $status ?? 'unknown' }}</span>
