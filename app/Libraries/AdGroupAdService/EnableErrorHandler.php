<?php

namespace App\Libraries\AdGroupAdService;

use App\Notifications\AdvertNotEnabled;
use App\Libraries\AdGroupAdService\ErrorHandler;

class EnableErrorHandler extends ErrorHandler
{
    public function handle()
    {
        $this->advertsWithErrors->each(function ($advert) {
            $this->user->notify(new AdvertNotEnabled($advert));
        });
    }
}
