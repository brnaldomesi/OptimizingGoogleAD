<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class PythonQueue extends Model
{
    use UuidModelTrait;
    use Notifiable;

    protected $dates = [

        'created_at',
        'updated_at',
        'processing_started_at',
        'processing_completed_at',
    ];

    protected $table = 'python_queue';
}
