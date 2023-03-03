<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Subscription;

class Plan extends Model
{
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function getPlan($currency, $accounts, $frequency = 'month', $user_limit = 1 )
    {
        // Frequency and user_limit are hard coded as we currently only have monthly plans for 1 user
        $plan = Plan::where('account_limit', '>=', $accounts)->orderBy('account_limit', 'asc')
            ->where('currency', $currency)
            ->where('frequency', $frequency)
            ->where('user_limit', $user_limit)->first();

        return $plan;
    }
}
