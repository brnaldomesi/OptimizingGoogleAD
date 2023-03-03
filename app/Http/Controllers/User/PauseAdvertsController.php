<?php

namespace App\Http\Controllers\User;

use Auth;
use App\Models\Advert;
use App\Models\Account;
use App\Models\Adgroup;
use App\Models\Campaign;
use App\Jobs\PauseAdverts;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\MutateAdGroupAdService;
use App\Libraries\AdWordsAPISession;
use App\Libraries\GetLosingAdvertsByAccount;
use App\Libraries\GetLosingAdvertsByAdgroup;
use App\Libraries\GetLosingAdvertsByCampaign;
use App\Libraries\AdGroupAdService\PauseOperations;
use App\Libraries\AdGroupAdService\PauseErrorHandler;
use App\Libraries\AdGroupAdService\PauseResponseHandler;

class PauseAdvertsController extends Controller
{
    public function __invoke(Request $request)
    {
        switch ($request->input('scope')) {

            case 'user': //not going to build this at this stage. this needs to be handled differently by looping through the accounts belonging to this user and doing a pause by account on each one.

                break;

            case 'account'://pause all losers in this account

                $account = Account::findOrFail($request->input('id'));

                $this->authorize('view', $account);

                $adverts = $account->advertsLoser(); //note to future programmers, calling the function becaust it's not an eloquent relationship

                $adWordsClientCustomerId = $account->google_id;

                break;

            case 'campaign'://pause all losers in this campaign

                $campaign = Campaign::findOrFail($request->input('id'));

                $this->authorize('view', $campaign->account);

                $adverts = $campaign->advertsLoser;

                $adWordsClientCustomerId = $campaign->account->google_id;

                break;

            case 'adgroup'://pause all losers in this adgroup

                $adgroup = Adgroup::findOrFail($request->input('id'));

                $this->authorize('view', $adgroup->campaign->account);

                $adverts = $adgroup->advertsLoser;

                $adWordsClientCustomerId = $adgroup->campaign->account->google_id;

                break;

            case 'advert'://pause one advert

                $adverts = Advert::where('id', $request->input('id'))->get();

                $this->authorize('view', $adverts[0]->adgroup->campaign->account);

                $adWordsClientCustomerId = $adverts[0]->adgroup->campaign->account->google_id;

                break;

            default:
                // code...
                break;
        }

        if (empty($adverts)) {
            return;
        }

        //make the change via the api
        $adWordsClientCustomerId = $adverts[0]->adgroup->campaign->account->google_id;

        $session = (new AdWordsAPISession($adWordsClientCustomerId, Auth::user()->refresh_token))->get();

        $operations = (new PauseOperations($adverts))->get();

        $responseHandler = new PauseResponseHandler($adverts);

        $errorHandler = new PauseErrorHandler(Auth::user(), $adverts, $operations);

        MutateAdGroupAdService::dispatch(
            $session,
            $operations,
            $responseHandler,
            $errorHandler
        );
    }
}
