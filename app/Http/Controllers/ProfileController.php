<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\UserCourseActivity;
use App\Models\Favorite;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = Auth::user();
        return view('profile.edit', ['user' => $user]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = $data['password'];
        }
        $user->save();


        return redirect()->route('profile.edit')->with('status', 'Profile updated');
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Incorrect password'])->withInput();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        UserCourseActivity::where('user_id', $user->id)->delete();
        Favorite::where('user_id', $user->id)->delete();
        $user->delete();

        return redirect('/')->with('status', 'Your account has been deleted');
    }
}

