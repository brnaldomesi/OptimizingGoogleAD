<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class AdNGramFeed extends Model
{
    use UuidModelTrait;
    use Notifiable;

    protected $table = 'ad_n_gram_feed';

    public function performance()
    {
        return $this->hasOne(\App\Models\AdNGramPerformance::class, 'id', 'n_gram_id');
    }
}
