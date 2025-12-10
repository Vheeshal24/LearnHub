<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function stats(Request $request)
    {
        $total = User::count();
        $admins = User::where('is_admin', true)->count();
        $newLast7 = User::where('created_at', '>=', now()->subDays(7))->count();
        return response()->json([
            'total' => (int) $total,
            'admins' => (int) $admins,
            'new_last_7' => (int) $newLast7,
        ]);
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));
        $role = $request->input('role');

        $query = User::query();
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'LIKE', "%$q%")
                    ->orWhere('email', 'LIKE', "%$q%");
            });
        }
        if ($role === 'admin') {
            $query->where('is_admin', true);
        } elseif ($role === 'user') {
            $query->where(function ($sub) {
                $sub->whereNull('is_admin')->orWhere('is_admin', false);
            });
        }

        $users = $query->orderByDesc('created_at')->paginate($request->integer('per_page', 15))->withQueryString();
        return response()->json($users);
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'is_admin' => ['sometimes', 'boolean'],
        ]);

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->is_admin = (bool)($data['is_admin'] ?? false);
        $user->save();

        return response()->json(['message' => 'User created successfully.', 'user' => $user], 201);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'is_admin' => ['sometimes', 'boolean'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->is_admin = (bool)($data['is_admin'] ?? false);
        $user->save();

        return response()->json(['message' => 'User updated successfully.', 'user' => $user]);
    }

    public function destroy(Request $request, User $user)
    {
        if (Auth::id() === $user->id) {
            return response()->json(['message' => 'You cannot delete your own account.'], 422);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully.']);
    }
}

