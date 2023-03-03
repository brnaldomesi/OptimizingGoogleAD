<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class KeywordPerformance extends Model
{
    use UuidModelTrait;
    use Notifiable;

    protected $table = 'keyword_performance';

    public function keyword()
    {
        return $this->hasOne(\App\Models\Keyword::class, 'id', 'keyword_id');
    }
}
