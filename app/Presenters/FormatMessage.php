<?php

namespace App\Presenters;

class FormatMessage
{
    protected $message;

    protected $readibleMessages = [

        'too_few_ads'		=>	'Too few ads',
        'too_many_ads'		=>	'Too many ads',
        'has_winners'		=>	'Has winners',
        'actions_available'	=>	'Actions Available',
        'okay'				=>	'Okay',
        'winning'			=>	'Winning',
        'losing'			=>	'Losing',
    ];

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function get()
    {
        if (! array_key_exists($this->message, $this->readibleMessages)) {
            return '';
        }

        return $this->readibleMessages[$this->message];
    }
}
