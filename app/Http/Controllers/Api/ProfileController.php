<?php
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
 
class ProfileController extends Controller
{
    // GET /api/profile
    public function show(Request $request)
    {
        return response()->json(['success' => true, 'user' => $request->user()]);
    }
 
    // PUT /api/profile
    public function update(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);
        $user->update($validated);
        return response()->json(['success' => true, 'user' => $user]);
    }
 
    // PUT /api/profile/password
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|string|min:6|confirmed',
        ]);
 
        $user = $request->user();
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Current password is incorrect.'],
            ]);
        }
 
        $user->update(['password' => Hash::make($request->new_password)]);
        return response()->json(['success' => true, 'message' => 'Password changed successfully']);
    }
}

