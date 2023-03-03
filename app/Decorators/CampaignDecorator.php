<?php

namespace App\Decorators;

use App\Models\CampaignPerformance;

class CampaignDecorator
{
    protected $collection; //eloquent collection

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function get()
    {
        return $this->collection->map(function ($campaign) {
            //IDs change when the python script runs meaning performance may not be available
            if ($campaign == null) {
                return;
            }

            if ($campaign->performance->isEmpty()) {
                $campaign->performance = new CampaignPerformance;
                $campaign->performance->campaign_id = $campaign->id;
            } else {
                $campaign->performance = $campaign->performance[0];
            }

            return $campaign;
        });
    }
}
