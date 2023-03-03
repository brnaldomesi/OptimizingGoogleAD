<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Libraries\AdGroupAdService\AddExpandedTextAdsOperations;
use App\Libraries\AdGroupAdService\AddExpandedTextAdsErrorHandler;

class AddExpandedTextAdsErrorHandlerTest extends TestCase
{
    /** @test **/
    public function user_is_notified_on_error()
    {
        $user = factory(\App\User::class)->create();

        $adgroup = factory(\App\Models\Adgroup::class)->make();

        $adverts = factory(\App\Models\Advert::class, 3)->make([
            'adgroup_id'	=>	$adgroup->id,
        ]);

        $operations = new AddExpandedTextAdsOperations($adverts);

        $handler = new AddExpandedTextAdsErrorHandler($user, $adverts, $operations);
        $handler->errors = [];
        $handler->handle();

        //3 ads created, all failed
        $this->assertEquals(3, $user->notifications->count());
    }
}
