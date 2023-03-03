<?php

namespace App\Libraries\CustomerService;

use Google\AdsApi\AdWords\v201802\cm\Selector;

class CustomerServiceSelector
{
    protected $fields; //array of fields to select

    public function __construct($fields)
    {
        $this->fields = $fields;
    }

    public function get()
    {
        $selector = new Selector();
        $selector->setFields($this->fields);

        return $selector;
    }
}
