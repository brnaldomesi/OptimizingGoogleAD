<?php

namespace App\Libraries\AdGroupAdService;

use Google\AdsApi\AdWords\v201802\cm\Ad;
use Google\AdsApi\AdWords\v201802\cm\Operator;
use Google\AdsApi\AdWords\v201802\cm\AdGroupAd;
use Google\AdsApi\AdWords\v201802\cm\AdGroupAdStatus;
use Google\AdsApi\AdWords\v201802\cm\AdGroupAdService;
use Google\AdsApi\AdWords\v201802\cm\AdGroupAdOperation;

class PauseOperations
{
    protected $adverts;

    public function __construct($adverts)
    {
        $this->adverts = $adverts;
    }

    public function get()
    {
        return $this->adverts->map(function ($advert) {
            $ad = new Ad();
            $ad->setId($advert->google_id);

            $adGroupAd = new AdGroupAd();
            $adGroupAd->setAdGroupId($advert->adgroup->google_id);
            $adGroupAd->setAd($ad);

            $adGroupAd->setStatus(AdGroupAdStatus::PAUSED);

            $operation = new AdGroupAdOperation();
            $operation->setOperand($adGroupAd);
            $operation->setOperator(Operator::SET);

            return $operation;
        })->toArray();
    }
}
