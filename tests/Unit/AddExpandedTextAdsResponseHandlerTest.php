<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Google\AdsApi\AdWords\v201802\cm\AdGroupAd;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Google\AdsApi\AdWords\v201802\cm\ExpandedTextAd;
use Google\AdsApi\AdWords\v201802\cm\AdGroupAdReturnValue;
use App\Libraries\AdGroupAdService\AddExpandedTextAdsResponseHandler;

class AddExpandedTextAdsResponseHandlerTest extends TestCase
{
    /** @test **/
    public function adverts_get_google_id()
    {
        $adgroup = factory(\App\Models\Adgroup::class)->create([
               'campaign_id'	=>	'test',
       ]);

        $adverts = factory(\App\Models\Advert::class, 3)->create([
            'adgroup_id'	=>	$adgroup->id,
            'google_id'		=>	null,
        ]);

        $values = $adverts->map(function ($advert) {
            $expandedTextAd = new ExpandedTextAd();
            $expandedTextAd->setHeadlinePart1($advert->headline_1);
            $expandedTextAd->setHeadlinePart2($advert->headline_2);
            $expandedTextAd->setDescription($advert->description);
            $expandedTextAd->setFinalUrls($advert->final_urls);
            $expandedTextAd->setPath1($advert->path_1);
            $expandedTextAd->setPath2($advert->path_2);
            $expandedTextAd->setId(md5($advert->path_1.$advert->path_2)); //reasonably random

            $adGroupAd = new AdGroupAd();
            $adGroupAd->setAdGroupId($advert->adgroup->google_id);
            $adGroupAd->setAd($expandedTextAd);

            return $adGroupAd;
        })->toArray();

        $response = new AdGroupAdReturnValue();
        $response->setValue($values);

        (new AddExpandedTextAdsResponseHandler($adverts))->handle($response);

        $adverts->each(function ($advert) {
            $this->assertNotNull($advert->google_id);
        });
    }
}
