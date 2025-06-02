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
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if($request->filled('password')) {
            // Validasi current_password wajib diisi
            $request->validate([
                'current_password' => 'required'
            ]);
            // Cek apakah current_password benar
            if (!Hash::check($request->current_password, $user->password)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'current_password' => 'Current password is incorrect.'
                ]);
            }
            $updateData['password'] = Hash::make($request->password);
        }

        // Tambahkan logika upload foto
        if ($request->hasFile('profile_picture')) {
            // Hapus foto lama jika ada
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            // Simpan foto baru
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $updateData['profile_picture'] = $path;
        }

        // Tambahkan log untuk debug
        \Log::info('Update Data:', $updateData);
        User::where('id', $user->id)->update($updateData);

        // Log data user setelah update
        $user->refresh();
        \Log::info('User after update:', $user->toArray());

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