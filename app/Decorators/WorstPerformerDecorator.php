<?php

namespace App\Decorators;

use App\Models\Advert;
use App\Models\AccountPerformance;

class WorstPerformerDecorator
{
    protected $collection; //eloquent collection

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function get()
    {
        return $this->collection->map(function ($worstPerformer) {
            $advert = Advert::find($worstPerformer->advert_id);

            $worstPerformer->headline_1 = $advert->headline_1;
            $worstPerformer->headline_2 = $advert->headline_2;
            $worstPerformer->final_urls = $advert->final_urls;
            $worstPerformer->domain = $advert->domain;
            $worstPerformer->path_1 = $advert->path_1;
            $worstPerformer->path_2 = $advert->path_2;
            $worstPerformer->description = $advert->description;
            $worstPerformer->adgroup_id = $advert->adgroup_id;

            return $worstPerformer;
        });
    }
}
