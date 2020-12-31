<?php

namespace App\Observers;

use App\Models\Cash;
use App\Models\User;

class UserObserver
{
    public function created(User $user)
    {
        $user->cashes()->create([
            'debit' => 0,
            'credit' => 0,
            'balance' => 0,
            'type' => 'topup',
            'description' => 'Create account'
        ]);

        // Cash::create([
        //     'id_user' => $user->id,
        //     'debit' => 0,
        //     'credit' => 0,
        //     'balance' => 0,
        //     'type' => 'topup',
        //     'description' => 'Create account'
        // ]);
    }
}
