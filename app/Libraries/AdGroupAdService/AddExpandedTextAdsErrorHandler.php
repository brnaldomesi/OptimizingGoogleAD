<?php

namespace App\Libraries\AdGroupAdService;

use App\Notifications\AdvertNotCreated;
use App\Libraries\AdGroupAdService\ErrorHandler;

class AddExpandedTextAdsErrorHandler extends ErrorHandler
{
    public function handle()
    {
        $this->advertsWithErrors->each(function ($advert) {
            $this->user->notify(new AdvertNotCreated($advert));
        });
    }
}
