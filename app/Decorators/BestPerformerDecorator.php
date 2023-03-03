<?php

namespace App\Decorators;

use App\Models\Advert;
use App\Models\AccountPerformance;

class BestPerformerDecorator
{
    protected $collection; //eloquent collection

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function get()
    {
        return $this->collection->map(function ($bestPerformer) {
            $advert = Advert::find($bestPerformer->advert_id);

            $bestPerformer->headline_1 = $advert->headline_1;
            $bestPerformer->headline_2 = $advert->headline_2;
            $bestPerformer->final_urls = $advert->final_urls;
            $bestPerformer->domain = $advert->domain;
            $bestPerformer->path_1 = $advert->path_1;
            $bestPerformer->path_2 = $advert->path_2;
            $bestPerformer->description = $advert->description;
            $bestPerformer->adgroup_id = $advert->adgroup_id;

            return $bestPerformer;
        });
    }
}
