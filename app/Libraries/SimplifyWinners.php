<?php

namespace App\Libraries;

class SimplifyWinners
{
    protected $winningElements;

    public function __construct($winningElements)
    {
        $this->winningElements = $winningElements;
    }

    public function get()
    {
        $winners = new \StdClass;

        $winners->headline_1 = $this->winningElements->where('type', 'headline_1')->pluck('value');

        $winners->headline_2 = $this->winningElements->where('type', 'headline_2')->pluck('value');

        $winners->description = $this->winningElements->where('type', 'description')->pluck('value');

        $winners->path = $this->winningElements->where('type', 'path_1_path_2')->pluck('value');

        return $winners;
    }
}
