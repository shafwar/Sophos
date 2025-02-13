<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        User::where('id', $user->id)->update($updateData);

        return back()->with('success', 'Profile updated successfully');
    }

    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user = Auth::user();

        if($user->profile_picture) {
            // Hapus foto lama jika ada
            if(Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
        }

        // Store file dengan nama unik
        $path = $request->file('profile_picture')->store('profile-pictures', 'public');
        
        // Update profile picture path di database
        User::where('id', $user->id)->update([
            'profile_picture' => $path
        ]);

        return back()->with('success', 'Profile picture uploaded successfully');
    }

    public function deletePhoto()
    {
        $user = Auth::user();

        if($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
            
            User::where('id', $user->id)->update([
                'profile_picture' => null
            ]);
        }

        return back()->with('success', 'Profile picture deleted successfully');
    }
}