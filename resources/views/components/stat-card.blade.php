@props(['icon', 'label', 'value', 'color' => 'primary'])

<div class="card border-0 shadow-sm h-100 stat-card">
    <div class="card-body d-flex align-items-center gap-3 p-3">
        <div class="stat-card__icon bg-{{ $color }}-subtle text-{{ $color }}">
            <i class="bi {{ $icon }}"></i>
        </div>
        <div class="min-w-0">
            <h3 class="fw-bold mb-0 lh-1">{{ $value }}</h3>
            <p class="text-muted mb-0 small">{{ $label }}</p>
        </div>
    </div>
</div>

@once
<style>
    .stat-card { transition: transform .15s ease, box-shadow .15s ease; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important; }
    .stat-card__icon {
        width: 52px; height: 52px; flex-shrink: 0; font-size: 1.35rem;
        border-radius: .75rem; display: flex; align-items: center; justify-content: center;
    }
</style>
@endonce
