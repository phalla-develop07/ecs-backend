<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — @yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>

    <div class="d-flex" style="min-height: 100vh;">

        {{-- Sidebar --}}
        <nav class="bg-dark text-white p-3" style="width: 250px; min-height: 100vh;">
            <h5 class="text-white mb-4">⚙️ Admin Panel</h5>
            <ul class="nav flex-column gap-1">
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'bg-secondary rounded' : '' }}"
                        href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('admin.categories.*') ? 'bg-secondary rounded' : '' }}"
                        href="{{ route('admin.categories.index') }}">
                        <i class="bi bi-tags me-2"></i> Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('admin.products.*') ? 'bg-secondary rounded' : '' }}"
                        href="{{ route('admin.products.index') }}">
                        <i class="bi bi-box-seam me-2"></i> Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('admin.users.*') ? 'bg-secondary rounded' : '' }}"
                        href="{{ route('admin.users.index') }}">
                        <i class="bi bi-people me-2"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('admin.orders.*') ? 'bg-secondary rounded' : '' }}"
                        href="{{ route('admin.orders.index') }}">
                        <i class="bi bi-cart3 me-2"></i> Orders
                        @if (isset($pendingOrdersCount) && $pendingOrdersCount > 0)
                            <span class="badge bg-danger ms-1">{{ $pendingOrdersCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('admin.reviews.*') ? 'bg-secondary rounded' : '' }}"
                        href="{{ route('admin.reviews.index') }}">
                        <i class="bi bi-star me-2"></i> Reviews
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('admin.wishlists.*') ? 'bg-secondary rounded' : '' }}"
                        href="{{ route('admin.wishlists.index') }}">
                        <i class="bi bi-heart me-2"></i> Wishlists
                    </a>
                </li>
                <li class="nav-item mt-4">
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </nav>

        {{-- Main content --}}
        <div class="flex-grow-1 bg-light">
            <header class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-muted">@yield('title', 'Dashboard')</h6>
                <span class="text-muted small">👤 {{ auth()->user()->name }}</span>
            </header>

            <main class="p-4">
                {{-- Flash messages --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
