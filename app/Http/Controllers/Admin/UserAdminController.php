<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));
        $role = $request->input('role'); // 'admin' or 'user' or null

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

        $users = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $stats = [
            'total' => User::count(),
            'admins' => User::where('is_admin', true)->count(),
            'new_last_7' => User::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('admin.users.index', [
            'users' => $users,
            'q' => $q,
            'role' => $role,
            'stats' => $stats,
        ]);
    }

    public function create()
    {
        return view('admin.users.create');
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

        return redirect()->route('admin.users.index')->with('status', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', ['user' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
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

        return redirect()->route('admin.users.index')->with('status', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('status', 'You cannot delete your own account.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('status', 'User deleted successfully.');
    }
}