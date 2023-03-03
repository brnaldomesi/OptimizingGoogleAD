<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Libraries\AdGroupAdService\EnableOperations;
use App\Libraries\AdGroupAdService\EnableErrorHandler;

class EnableErrorHandlerTest extends TestCase
{
    /** @test **/
    public function user_is_notified_on_error()
    {
        $user = factory(\App\User::class)->create();

        $adgroup = factory(\App\Models\Adgroup::class)->make();

        $adverts = factory(\App\Models\Advert::class, 3)->make([
            'adgroup_id'	=>	$adgroup->id,
        ]);

        $operations = new EnableOperations($adverts);

        $handler = new EnableErrorHandler($user, $adverts, $operations);
        $handler->errors = [];
        $handler->handle();

        //3 ads created, all failed
        $this->assertEquals(3, $user->notifications->count());
    }
}
