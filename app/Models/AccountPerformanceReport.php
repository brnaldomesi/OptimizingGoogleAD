<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laracodes\Presenter\Traits\Presentable;

class AccountPerformanceReport extends Model
{
    use UuidModelTrait;
    use Notifiable;
    use Presentable;

    protected $table = 'account_performance_reports';

    public function account_performance_report()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }
}
