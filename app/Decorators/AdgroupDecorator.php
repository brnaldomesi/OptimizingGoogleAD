<?php

namespace App\Decorators;

use App\Models\AdgroupPerformance;

class AdgroupDecorator
{
    protected $collection; //eloquent collection

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function get()
    {
        return $this->collection->map(function ($adgroup) {
            if ($adgroup == null) {
                return;
            }

            if ($adgroup->performance->isEmpty()) {
                $adgroup->performance = new AdgroupPerformance;
                $adgroup->performance->adgroup_id = $adgroup->id;
            } else {
                $adgroup->performance = $adgroup->performance[0];
            }

            return $adgroup;
        });
    }
}
