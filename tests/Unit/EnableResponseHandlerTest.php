<?php

namespace Tests\Unit;

use Tests\TestCase;
use Google\AdsApi\AdWords\v201802\cm\Ad;
use Illuminate\Foundation\Testing\WithFaker;
use Google\AdsApi\AdWords\v201802\cm\AdGroupAd;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Google\AdsApi\AdWords\v201802\cm\AdGroupAdStatus;
use App\Libraries\AdGroupAdService\EnableResponseHandler;
use Google\AdsApi\AdWords\v201802\cm\AdGroupAdReturnValue;

class EnableResponseHandlerTest extends TestCase
{
    /** @test **/
    public function adverts_status_is_set_to_enabled()
    {
        $adgroup = factory(\App\Models\Adgroup::class)->create([
               'campaign_id'	=>	'test',
           ]);

        //create some paused ads
        $adverts = factory(\App\Models\Advert::class, 3)->create([
            'adgroup_id'	=>	$adgroup->id,
            'status'		=>	'paused',
        ]);

        $values = $adverts->map(function ($advert) {
            $ad = new Ad();
            $ad->setId($advert->google_id);

            $adGroupAd = new AdGroupAd();
            $adGroupAd->setAdGroupId($advert->adgroup->google_id);
            $adGroupAd->setAd($ad);
            $adGroupAd->setStatus(AdGroupAdStatus::ENABLED);

            return $adGroupAd;
        })->toArray();

        $response = new AdGroupAdReturnValue();
        $response->setValue($values);

        (new EnableResponseHandler($adverts))->handle($response);

        $adverts->each(function ($advert) {
            $this->assertEquals('enabled', $advert->status);
        });
    }
}
