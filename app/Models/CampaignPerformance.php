<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laracodes\Presenter\Traits\Presentable;

class CampaignPerformance extends Model
{
    use UuidModelTrait;
    use Notifiable;
    use Presentable;

    protected $presenter = \App\Presenters\CampaignPerformancePresenter::class;

    protected $table = 'campaign_performance';

    public function campaign()
    {
        return $this->belongsTo(\App\Models\Campaign::class);
    }
}
