<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class PythonLog extends Model
{
    use UuidModelTrait;
    use Notifiable;

    protected $dates = [
        'created_at'
    ];

    protected $table = 'python_log';
}
