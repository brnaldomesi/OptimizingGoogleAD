<?php

namespace App\Libraries;

//@todo:refactor this into a user presenter

class HumanReadibleDateRange
{
    protected $dateRange;

    protected $readibleDateRanges = [

        'all_time'		=>	'All time',
        'last_30_days'	=>	'Last 30 days',
        'last_7_days'	=>	'Last 7 days',
        'yesterday'		=>	'Yesterday',
        'today'			=>	'Today',
    ];

    public function __construct($date_range)
    {
        $this->dateRange = $date_range;
    }

    public function get()
    {
        if (! array_key_exists($this->dateRange, $this->readibleDateRanges)) {
            return '';
        }

        return $this->readibleDateRanges[$this->dateRange];
    }
}
