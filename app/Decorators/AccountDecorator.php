<?php

namespace App\Decorators;

use App\Models\AccountPerformance;

class AccountDecorator
{
    protected $collection; //eloquent collection

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function get()
    {
        return $this->collection->map(function ($account) {
            if ($account->performance->isEmpty()) {
                $account->performance = new AccountPerformance;
                $account->performance->account_id = $account->id;
            } else {
                $account->performance = $account->performance[0];
            }
            return $account;
        });
    }
}
