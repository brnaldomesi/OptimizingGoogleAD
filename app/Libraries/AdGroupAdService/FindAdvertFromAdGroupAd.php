<?php

namespace App\Libraries\AdGroupAdService;

use App\Models\Adgroup;

//given google's adgroupad returns a matching advert if any from our adverts collection

//@todo: refactor this to use the advert's google_id if it's available
class FindAdvertFromAdGroupAd
{
    protected $adverts; //our advert collection

    protected $adGroupAd; //google's representation

    public function __construct($adverts, $adGroupAd)
    {
        $this->adverts = $adverts;
        $this->adGroupAd = $adGroupAd;
    }

    public function get()
    {
        $adgroup = Adgroup::where('google_id', $this->adGroupAd->getAdGroupId())->first();

        $adGroupAd = $this->adGroupAd;

        //can we get it by google's id?
        $advert = $this->adverts->first(function ($advert) use ($adGroupAd, $adgroup) {
            if (
                $advert->google_id == $adGroupAd->getAd()->getId() &&

                $advert->adgroup_id == $adgroup->id

            ) {
                return $advert;
            }
        });

        if ($advert) {
            return $advert;
        }

        //can't find the ad using the adgroup and google id so find it by the elements

        return $this->adverts->first(function ($advert) use ($adGroupAd, $adgroup) {
            if (
                $advert->adgroup_id == $adgroup->id &&

                $advert->headline_1 == $adGroupAd->getAd()->getHeadlinePart1() &&

                $advert->headline_2 == $adGroupAd->getAd()->getHeadlinePart2() &&

                $advert->path_1 == $adGroupAd->getAd()->getPath1() &&

                $advert->path_2 == $adGroupAd->getAd()->getPath2() &&

                $advert->description == $adGroupAd->getAd()->getDescription()) {
                return $advert;
            }
        });
    }
}
