<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\StoreUserRequest;
use App\Http\Requests\Dashboard\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Roles that may be assigned through the dashboard. The 'customer' role is
     * intentionally excluded — those accounts are auto-provisioned by the
     * mobile phone-login endpoint, not managed manually here.
     */
    private const ASSIGNABLE_ROLES = ['admin', 'engineer', 'viewer'];

    public function index(Request $request): Response
    {
        $users = User::query()
            ->whereIn('role', self::ASSIGNABLE_ROLES)
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('dashboard/users/Index', [
            'users' => $users,
            'currentUserId' => $request->user()->id,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('dashboard/users/Create', [
            'roles' => self::ASSIGNABLE_ROLES,
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        // Password is hashed automatically via the User model's `password` cast.
        User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'role' => $request->validated('role'),
            'is_active' => true,
        ]);

        return redirect()->route('dashboard.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('dashboard/users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'roles' => self::ASSIGNABLE_ROLES,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->safe()->except(['password', 'password_confirmation']);

        if ($request->filled('password')) {
            $data['password'] = $request->validated('password');
        }

        $user->update($data);

        return redirect()->route('dashboard.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('dashboard.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
