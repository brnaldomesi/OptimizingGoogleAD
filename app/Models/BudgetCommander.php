<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laracodes\Presenter\Traits\Presentable;

class BudgetCommander extends Model
{

    use UuidModelTrait;
    use Notifiable;
    use Presentable;

    protected $table = 'budget_commander';

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function campaign()
    {
        return $this->hasMany(\App\Models\Campaign::class, 'budget_group_id', 'id');
    }
    
}
