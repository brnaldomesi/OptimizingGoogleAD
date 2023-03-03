<?php

namespace App\Http\Controllers\User;

use Auth;
use App\Models\Advert;
use App\Models\Adgroup;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAdvert;
use App\Libraries\DomainNameParser;
use App\Http\Controllers\Controller;
//use App\Jobs\AddExpandedTextAdverts;
//use App\Libraries\FormatAdvertsToAdd;
use App\Jobs\MutateAdGroupAdService;
use App\Libraries\AdWordsAPISession;
use App\Libraries\AdGroupAdService\AddExpandedTextAdsOperations;
use App\Libraries\AdGroupAdService\AddExpandedTextAdsErrorHandler;
use App\Libraries\AdGroupAdService\AddExpandedTextAdsResponseHandler;

class CreateAdvertController extends Controller
{
    public function __invoke(StoreAdvert $request)
    {
        $adgroup = Adgroup::findOrFail($request->input('adgroup_id'));

        $advert = new Advert();
        $advert->adgroup_id = $request->input('adgroup_id');
        $advert->headline_1 = $request->input('headline_1');
        $advert->headline_2 = $request->input('headline_2');
        $advert->final_urls = [$request->input('final_urls')];
        $advert->path_1 = $request->input('path_1');
        $advert->path_2 = $request->input('path_2');
        $advert->description = $request->input('description');

        $advert->domain = (new DomainNameParser($request->input('final_urls')))->get();
        $advert->status = 'enabled';
        $advert->save();

        $adverts = collect([$advert]);

        //make  request  to google
        $adWordsClientCustomerId = $adgroup->campaign->account->google_id;

        $adWordsAPISession = new AdWordsAPISession($adWordsClientCustomerId, Auth::user()->refresh_token);

        $session = $adWordsAPISession->get();

        $operations = (new AddExpandedTextAdsOperations($adverts))->get();

        $responseHandler = new AddExpandedTextAdsResponseHandler($adverts);

        $errorHandler = new AddExpandedTextAdsErrorHandler(Auth::user(), $adverts, $operations);

        MutateAdGroupAdService::dispatch(
            $session,
            $operations,
            $responseHandler,
            $errorHandler
        );

        return redirect('user/adverts/'.$adgroup->id);
    }
}
