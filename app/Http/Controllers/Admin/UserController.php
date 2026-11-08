<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Location;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with(['role', 'department', 'location'])
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
            );
        }
        if ($request->filled('role')) {
            $query->whereHas('role', fn($q) => $q->where('slug', $request->role));
        }

        $users     = $query->paginate(20)->withQueryString();
        $roles     = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create(): View
    {
        $roles       = Role::orderBy('name')->get();
        $departments = Department::where('is_active', true)->get();
        $locations   = Location::where('is_active', true)->get();

        return view('admin.users.create', compact('roles', 'departments', 'locations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'       => ['required', 'string', 'min:8', 'confirmed'],
            'role_id'        => ['required', 'exists:roles,id'],
            'department_id'  => ['nullable', 'exists:departments,id'],
            'location_id'    => ['nullable', 'exists:locations,id'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'job_title'      => ['nullable', 'string', 'max:100'],
            'is_active'      => ['boolean'],
        ]);

        User::create([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'password'      => Hash::make($validated['password']),
            'role_id'       => $validated['role_id'],
            'department_id' => $validated['department_id'] ?? null,
            'location_id'   => $validated['location_id'] ?? null,
            'phone'         => $validated['phone'] ?? null,
            'job_title'     => $validated['job_title'] ?? null,
            'is_active'     => $validated['is_active'] ?? true,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "User {$validated['name']} berhasil dibuat.");
    }

    public function edit(User $user): View
    {
        $roles       = Role::orderBy('name')->get();
        $departments = Department::where('is_active', true)->get();
        $locations   = Location::where('is_active', true)->get();

        return view('admin.users.edit', compact('user', 'roles', 'departments', 'locations'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role_id'        => ['required', 'exists:roles,id'],
            'department_id'  => ['nullable', 'exists:departments,id'],
            'location_id'    => ['nullable', 'exists:locations,id'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'job_title'      => ['nullable', 'string', 'max:100'],
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} berhasil diperbarui.");
    }

    public function toggleActive(User $user): RedirectResponse
    {
        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "User {$user->name} telah {$status}.");
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', "Password {$user->name} telah direset.");
    }
}
