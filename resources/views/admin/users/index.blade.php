@extends('admin.layouts.app')
@section('title', 'Users')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Users</h4>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control form-control-sm" style="width:220px;"
                    placeholder="Search name or email..." value="{{ request('search') }}">
                <button class="btn btn-sm btn-primary">Search</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Avatar</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Orders</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="text-muted small">{{ $loop->iteration }}</td>
                            {{-- Avatar --}}
                            <td>
                                @if($user->avatar)
                                    <img src="{{ Storage::url($user->avatar) }}"
                                         alt="{{ $user->name }}"
                                         style="width:40px;height:40px;object-fit:cover;border-radius:50%;">
                                @else
                                    {{-- Fallback: initials circle --}}
                                    <div style="width:40px;height:40px;border-radius:50%;background:#e0e7ff;
                                                display:flex;align-items:center;justify-content:center;
                                                font-weight:600;font-size:0.85rem;color:#6366f1;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $user->name }}</td>
                            <td class="text-muted small">{{ $user->email }}</td>
                            <td>{{ $user->orders()->count() }}</td>
                            <td class="text-muted small">
                                {{ $user->created_at ? $user->created_at->format('M d, Y') : '—' }}
                            </td>
                            <td>
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline"
                                    onsubmit="return confirm('Delete this user?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $users->links() }}</div>
@endsection
