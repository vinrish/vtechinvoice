<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\User\CreateUser;
use App\Actions\User\DeleteUser;
use App\Actions\User\UpdateUser;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $users = User::query()->select('id', 'name', 'email', 'created_at')->latest()->paginate(20);

        return Inertia::render('users/Index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('users/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateUserRequest $request, CreateUser $createUser): RedirectResponse
    {
        $createUser->handle($request->validated());

        return to_route('users.index')->with('status', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): Response
    {
        return Inertia::render('users/Show', [
            'user' => $user->only('id', 'name', 'email', 'created_at'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): Response
    {
        return Inertia::render('users/Edit', $user->only('id', 'name', 'email'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user, UpdateUser $updateUser): RedirectResponse
    {
        $updateUser->handle($user, $request->validated());

        return to_route('users.index')->with('status', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, DeleteUser $deleteUser): RedirectResponse
    {
        $deleteUser->handle($user);

        return to_route('users.index')->with('status', 'User deleted successfully.');
    }
}
