<?php

namespace App\Http\Controllers\User;

use Auth;
use App\Models\Advert;
use App\Models\Account;
use App\Models\Adgroup;
use App\Models\Campaign;
use App\Jobs\EnableAdverts;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\MutateAdGroupAdService;
use App\Libraries\AdWordsAPISession;
use App\Libraries\AdGroupAdService\EnableOperations;
use App\Libraries\AdGroupAdService\EnableErrorHandler;
use App\Libraries\AdGroupAdService\EnableResponseHandler;

class EnableAdvertsController extends Controller
{
    public function __invoke(Request $request)
    {
        $adverts = Advert::with('adgroup')
            ->where('id', $request->input('id'))
            ->get();

        //check if user is allowed to do this
        $this->authorize('view', $adverts[0]->adgroup->campaign->account);

        //make the change via the api
        $adWordsClientCustomerId = $adverts[0]->adgroup->campaign->account->google_id;

        $session = (new AdWordsAPISession($adWordsClientCustomerId, Auth::user()->refresh_token))->get();

        $operations = (new EnableOperations($adverts))->get();

        $responseHandler = new EnableResponseHandler($adverts);

        $errorHandler = new EnableErrorHandler(Auth::user(), $adverts, $operations);

        MutateAdGroupAdService::dispatch(
            $session,
            $operations,
            $responseHandler,
            $errorHandler
        );
    }
}
