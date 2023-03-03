<?php

namespace App\Libraries\AdGroupAdService;

//use Google\AdsApi\AdWords\v201802\cm\Ad;
use Google\AdsApi\AdWords\v201802\cm\Operator;
use Google\AdsApi\AdWords\v201802\cm\AdGroupAd;
use Google\AdsApi\AdWords\v201802\cm\ExpandedTextAd;
//use Google\AdsApi\AdWords\v201802\cm\AdGroupAdService;
//use Google\AdsApi\AdWords\v201802\cm\AdGroupAdStatus;
use Google\AdsApi\AdWords\v201802\cm\AdGroupAdOperation;

class AddExpandedTextAdsOperations
{
    protected $adverts;

    public function __construct($adverts)
    {
        $this->adverts = $adverts;
    }

    public function get()
    {
        return $this->adverts->map(function ($advert) {
            $expandedTextAd = new ExpandedTextAd();
            $expandedTextAd->setHeadlinePart1($advert->headline_1);
            $expandedTextAd->setHeadlinePart2($advert->headline_2);
            $expandedTextAd->setDescription($advert->description);
            $expandedTextAd->setFinalUrls($advert->final_urls);
            $expandedTextAd->setPath1($advert->path_1);
            $expandedTextAd->setPath2($advert->path_2);

            $adGroupAd = new AdGroupAd();
            $adGroupAd->setAdGroupId($advert->adgroup->google_id);
            $adGroupAd->setAd($expandedTextAd);
            //$adGroupAd->setStatus(AdGroupAdStatus::ENABLED);//not sure if we need this

            $operation = new AdGroupAdOperation();
            $operation->setOperand($adGroupAd);
            $operation->setOperator(Operator::ADD);
            $operations[] = $operation;

            return $operation;
        })->toArray();
    }
}
