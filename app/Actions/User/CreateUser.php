<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final readonly class CreateUser
{
    /**
     * @param  array{ name: string, email: string, password: string }  $data
     */
    public function handle(array $data): User
    {
        return DB::transaction(fn () => User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]));
    }
}
