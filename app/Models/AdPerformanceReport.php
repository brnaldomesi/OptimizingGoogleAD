<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class AdPerformanceReport extends Model
{
    use UuidModelTrait;
    use Notifiable;

    protected $dates = [

        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'account_id',
        'date',
        'advert_google_id',
        'campaign_google_id',
        'campaign_name',
        'campaign_status',
        'adgroup_google_id',
        'adgroup_name',
        'adgroup_status',
        'headline_1',
        'headline_2',
        'description',
        'path_1',
        'path_2',
        'advert_status',
        'final_urls',
        'impressions',
        'clicks',
        'conversions',
        'cost',
        'conversion_value',
    ];

    public function account()
    {
        return $this->belongsTo('App\Account');
    }
}
