<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Account;
use App\Libraries\AdWordsAPISession;
use App\Jobs\GetOneAccountFromGoogle;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetOneAccountFromGoogleTest extends TestCase
{
    /** @test **/
    public function account_is_created()
    {
        $googleAccountId = env('LIVE_ADWORDS_ACCOUNT');
        $refreshToken = env('LIVE_ADWORDS_REFRESH_TOKEN');

        //delete all instances of the account from the db just in case they were left over from earlier testing
        Account::where('google_id', $googleAccountId)->delete();

        //call the job
        $adWordsAPISession = new AdWordsAPISession($googleAccountId, $refreshToken);

        $session = $adWordsAPISession->get();

        GetOneAccountFromGoogle::dispatch($session, 'test_user_id');

        //find the newly created account in the db
        $account = Account::where('google_id', $googleAccountId)->first();

        $this->assertEquals($googleAccountId, $account->google_id);
    }
}
