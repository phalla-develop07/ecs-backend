<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'user')->latest();

        if ($request->filled('search')) {
            $query->where(
                fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
            );
        }

        $users = $query->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->loadCount(['orders', 'reviews', 'wishlists']);

        $orders    = $user->orders()->latest()->get();
        $reviews   = $user->reviews()->with('product')->latest()->get();
        $wishlists = $user->wishlists()->with('product')->latest()->get();

        return view('admin.users.show', compact('user', 'orders', 'reviews', 'wishlists'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'avatar'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
