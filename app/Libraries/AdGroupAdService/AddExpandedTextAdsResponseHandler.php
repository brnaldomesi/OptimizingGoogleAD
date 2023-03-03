<?php

namespace App\Libraries\AdGroupAdService;

use App\Libraries\AdGroupAdService\ResponseHandler;
use App\Libraries\AdGroupAdService\FindAdvertFromAdGroupAd;

class AddExpandedTextAdsResponseHandler extends ResponseHandler
{
    public function handle($response)
    {
        foreach ($response->getValue() as $adGroupAd) {
            if ($advert = (new FindAdvertFromAdGroupAd($this->adverts, $adGroupAd))->get()) {
                $advert->google_id = $adGroupAd->getAd()->getId();
                $advert->save();
            } else {

                //@todo: throw exception
            }
        }
    }
}
