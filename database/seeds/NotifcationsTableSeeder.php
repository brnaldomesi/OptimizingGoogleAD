<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationsTableSeeder extends Seeder
{
    public function run()
    {
        foreach (User::all() as $user) {
            factory(App\Models\Notification::class, 10)->create([
                'user_id'	=>	$user->id,
            ]);
        }
    }
}
