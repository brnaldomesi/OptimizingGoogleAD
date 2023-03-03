<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class KeywordFeed extends Model
{
    use UuidModelTrait;
    use Notifiable;

    protected $table = 'keyword_feed';

    public function keyword()
    {
        return $this->hasOne(\App\Models\Keyword::class, 'id', 'keyword_id');
    }

    public function performance()
    {
        return $this->hasOne(\App\Models\KeywordPerformance::class, 'keyword_id', 'keyword_id');
    }
}
