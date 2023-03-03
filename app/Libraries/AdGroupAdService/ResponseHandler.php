<?php

namespace App\Libraries\AdGroupAdService;

use App\Models\Adgroup;

class ResponseHandler
{
    protected $adverts;

    public function __construct($adverts)
    {
        $this->adverts = $adverts;
    }
}
