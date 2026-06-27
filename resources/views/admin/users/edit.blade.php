{{-- resources/views/admin/users/edit.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'Edit User')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Edit User</h4>
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card border-0 shadow-sm" style="max-width: 600px;">
        <div class="card-body">

            <form method="POST" action="{{ route('admin.users.update', $user) }}"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Avatar --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Avatar</label>

                    {{-- Current avatar or initials fallback --}}
                    <div class="mb-2">
                        @if($user->avatar)
                            <img id="avatarPreview"
                                 src="{{ Storage::url($user->avatar) }}"
                                 alt="{{ $user->name }}"
                                 style="width:72px;height:72px;object-fit:cover;border-radius:50%;border:2px solid #e5e7eb;">
                        @else
                            <div id="avatarInitials"
                                 style="width:72px;height:72px;border-radius:50%;background:#e0e7ff;
                                        display:flex;align-items:center;justify-content:center;
                                        font-weight:700;font-size:1.4rem;color:#6366f1;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <img id="avatarPreview" src="#" alt="Preview"
                                 class="d-none"
                                 style="width:72px;height:72px;object-fit:cover;border-radius:50%;border:2px solid #e5e7eb;">
                        @endif
                    </div>

                    <input type="file" name="avatar" accept="image/*"
                           class="form-control @error('avatar') is-invalid @enderror"
                           onchange="previewAvatar(this)">
                    <div class="form-text">JPG, PNG, WEBP — max 2MB. Leave empty to keep current.</div>
                    @error('avatar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Name --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Full Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $user->name) }}"
                           placeholder="e.g. John Doe">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Email Address <span class="text-danger">*</span>
                    </label>
                    <input type="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}"
                           placeholder="e.g. john@example.com">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- New Password --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">New Password</label>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Leave blank to keep current password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Confirm New Password</label>
                    <input type="password" name="password_confirmation"
                           class="form-control"
                           placeholder="Repeat new password">
                </div>

                {{-- Actions --}}
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Update User
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function previewAvatar(input) {
    const preview  = document.getElementById('avatarPreview');
    const initials = document.getElementById('avatarInitials');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
            // Hide initials div if it exists (no existing avatar case)
            if (initials) initials.classList.add('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush