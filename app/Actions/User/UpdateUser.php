<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class UpdateUser
{
    /**
     * @param  array{ name: string, email: string }  $data
     */
    public function handle(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);

            return $user->refresh();
        });
    }
}
