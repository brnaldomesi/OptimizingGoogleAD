<?php

namespace App\Libraries\AdGroupAdService;

use App\Notifications\AdvertNotPaused;
use App\Libraries\AdGroupAdService\ErrorHandler;

class PauseErrorHandler extends ErrorHandler
{
    public function handle()
    {
        $this->advertsWithErrors->each(function ($advert) {
            $this->user->notify(new AdvertNotPaused($advert));
        });
    }
}
