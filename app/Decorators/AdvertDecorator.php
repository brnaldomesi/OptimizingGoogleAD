<?php

namespace App\Decorators;

use App\Models\AdvertPerformance;

class AdvertDecorator
{
    protected $collection; //eloquent collection

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function get()
    {
        return $this->collection->map(function ($advert) {
            if ($advert->performance->isEmpty()) {
                $advert->performance = new AdvertPerformance;
                $advert->performance->advert_id = $advert->id;
            } else {
                $advert->performance = $advert->performance[0];
            }

            return $advert;
        });
    }
}
