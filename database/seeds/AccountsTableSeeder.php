<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AccountsTableSeeder extends Seeder
{
    public function run()
    {
        foreach (User::all() as $user) {
            factory(App\Models\Account::class, 20)->create([
                'user_id'	=>	$user->id
            ]);
        }
    }
}
