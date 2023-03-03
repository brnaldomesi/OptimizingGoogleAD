<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class AdNGramPerformance extends Model
{
    use UuidModelTrait;
    use Notifiable;

    protected $casts = [

        'show_on_graph'	=>	'boolean',
    ];

    protected $table = 'ad_n_gram_performance';

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

}
