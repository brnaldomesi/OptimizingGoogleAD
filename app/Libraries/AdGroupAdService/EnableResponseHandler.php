<?php

namespace App\Libraries\AdGroupAdService;

use App\Libraries\AdGroupAdService\ResponseHandler;
use App\Libraries\AdGroupAdService\FindAdvertFromAdGroupAd;

class EnableResponseHandler extends ResponseHandler
{
    public function handle($response)
    {
        foreach ($response->getValue() as $adGroupAd) {
            if ($advert = (new FindAdvertFromAdGroupAd($this->adverts, $adGroupAd))->get()) {
                $advert->status = strtolower($adGroupAd->getStatus());

                $advert->save();
            } else {

                //@todo: throw exception
            }
        }
    }
}
