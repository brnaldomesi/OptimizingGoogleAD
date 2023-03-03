<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Google\AdsApi\AdWords\v201802\cm\AdGroupAd;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Google\AdsApi\AdWords\v201802\cm\ExpandedTextAd;
use App\Libraries\AdGroupAdService\FindAdvertFromAdGroupAd;

class FindAdvertFromAdGroupAdTest extends TestCase
{
    /** @test **/
    public function advert_with_google_id_is_found()
    {
        $adgroup = factory(\App\Models\Adgroup::class)->create([
            'campaign_id'	=>	'test',
        ]);

        $adverts = factory(\App\Models\Advert::class, 1)->create([
            'adgroup_id'	=>	$adgroup->id,
        ]);

        $advert = $adverts->first();

        //first try - find with only google id
        $ad = new ExpandedTextAd;
        $ad->setId($advert->google_id);

        $adGroupAd = new AdGroupAd();
        $adGroupAd->setAdGroupId($advert->adgroup->google_id);
        $adGroupAd->setAd($ad);

        $foundAdvert = (new FindAdvertFromAdGroupAd($adverts, $adGroupAd))->get();

        $this->assertEquals($foundAdvert->id, $advert->id);
    }

    /** @test **/
    public function advert_without_google_id_is_found()
    {
        $adgroup = factory(\App\Models\Adgroup::class)->create([
            'campaign_id'	=>	'test',
        ]);

        $adverts = factory(\App\Models\Advert::class, 1)->create([
            'adgroup_id'	=>	$adgroup->id,
        ]);

        $advert = $adverts->first();

        //now try without google id
        $ad = new ExpandedTextAd;
        $ad->setHeadlinePart1($advert->headline_1);
        $ad->setHeadlinePart2($advert->headline_2);
        $ad->setDescription($advert->description);
        $ad->setFinalUrls($advert->final_urls);
        $ad->setPath1($advert->path_1);
        $ad->setPath2($advert->path_2);

        $adGroupAd = new AdGroupAd();
        $adGroupAd->setAdGroupId($advert->adgroup->google_id);
        $adGroupAd->setAd($ad);

        $foundAdvert = (new FindAdvertFromAdGroupAd($adverts, $adGroupAd))->get();

        $this->assertEquals($foundAdvert->id, $advert->id);
    }

    /** @test **/
    public function advert_with_incorrect_google_id_is_not_found()
    {
        $adgroup = factory(\App\Models\Adgroup::class)->create([
            'campaign_id'	=>	'test',
        ]);

        $adverts = factory(\App\Models\Advert::class, 1)->create([
            'adgroup_id'	=>	$adgroup->id,
        ]);

        $advert = $adverts->first();

        //first try - find with only google id
        $ad = new ExpandedTextAd;
        $ad->setId('incorrect');

        $adGroupAd = new AdGroupAd();
        $adGroupAd->setAdGroupId($advert->adgroup->google_id);
        $adGroupAd->setAd($ad);

        $foundAdvert = (new FindAdvertFromAdGroupAd($adverts, $adGroupAd))->get();

        $this->assertNull($foundAdvert);
    }

    /** @test **/
    public function advert_with_non_matching_elements_is_not_found()
    {
        $adgroup = factory(\App\Models\Adgroup::class)->create([
            'campaign_id'	=>	'test',
        ]);

        $adverts = factory(\App\Models\Advert::class, 1)->create([
            'adgroup_id'	=>	$adgroup->id,
        ]);

        $advert = $adverts->first();

        //now try without google id
        $ad = new ExpandedTextAd;
        $ad->setHeadlinePart1('incorrect headline');
        $ad->setHeadlinePart2($advert->headline_2);
        $ad->setDescription($advert->description);
        $ad->setFinalUrls($advert->final_urls);
        $ad->setPath1($advert->path_1);
        $ad->setPath2($advert->path_2);

        $adGroupAd = new AdGroupAd();
        $adGroupAd->setAdGroupId($advert->adgroup->google_id);
        $adGroupAd->setAd($ad);

        $foundAdvert = (new FindAdvertFromAdGroupAd($adverts, $adGroupAd))->get();

        $this->assertNull($foundAdvert);
    }
}
