<?php

namespace App\Http\Controllers\api;

use Log;
use DB;
use Auth;
use Carbon\Carbon;
use App\Models\AdvertFeed;
use App\Models\AdGroupFeed;
use App\Models\AdNGramFeed;
use App\Models\KeywordFeed;
use Illuminate\Http\Request;
use App\Models\SearchQueryFeed;
use App\Http\Controllers\Controller;
use App\Models\SearchQueryNGramFeed;

class FeedController extends Controller
{

    var $results_per_page = 6;

    public function new(\App\Models\Account $account)
    {

        $include_snoozed = false;
        $data = $this->api($account, $include_snoozed);
        $data =  collect(
            $data->where('read', '!=', 1)->where('archived', '!=', 1)
            )->paginate($this->results_per_page);
            // dd($data);
            return $data;
    }
    
    public function read(\App\Models\Account $account)
    {

        $include_snoozed = false;
        $data = $this->api($account, $include_snoozed);
        //it's read but not archived (archived takes precedence)
        return collect($data->where('read', 1)->where('archived', '!=', 1))->paginate($this->results_per_page);
    }

    public function archived(\App\Models\Account $account)
    {

        $include_snoozed = false;
        $data = $this->api($account, $include_snoozed);
        return collect($data->where('archived', 1))->paginate($this->results_per_page);
    }
    
    public function snoozed(\App\Models\Account $account)
    {

      // if($account->user->id != Auth::id()){
      //   return 'unauthorised';
      // }

        $include_snoozed = true;
        
        //rows which aren't read or archived, but have been snoozed
        $data = $this->api($account, $include_snoozed)->where('read', '!=', 1)->where('archived', '!=', 1);
        $date = Carbon::today()->toDateString();
        $data = $data->filter(function ($item) use ($date) {
            return data_get($item, 'snooze_until_date') > $date;
        });
        
        return collect($data)->paginate($this->results_per_page);
    }
    
    private function api($account, $include_snoozed)
    {
        $feed = $this->getKeywordFeed($account, $include_snoozed)
        ->merge($this->getQueryFeed($account, $include_snoozed))
        ->merge($this->getAdGroupFeed($account, $include_snoozed))
            ->merge($this->getAdvertFeed($account, $include_snoozed))
            ->merge($this->getSearchQueryNGramFeed($account, $include_snoozed))
            ->sortByDesc('date_range');
            
        $values = $feed->values();
        return $values;
       

    }

    private function getSearchQueryNGramFeed($account, $include_snoozed)
    {
        $feed = SearchQueryNGramFeed::where('account_id', $account->id)->take(10);
        if($include_snoozed){
            $feed = $feed->whereNotNull('snooze_until_date');
        }else{
            $feed = $feed->whereNull('snooze_until_date');
        }
        $feed = $feed->whereDate('display_from_date', '<=', Carbon::today()->toDateString());
        $feed = $feed->get();

        foreach ($feed as $key => $value) {
            $feed[$key]->performance = $feed[$key]->performance->where("date_range", $feed[$key]->date_range)->first();
        }

        return $feed;
    }

    private function getAdGroupFeed($account, $include_snoozed)
    {
        $feed = AdGroupFeed::where('account_id', $account->id)->take(10);
        if($include_snoozed){
            $feed = $feed->whereNotNull('snooze_until_date');
        }else{
            $feed = $feed->whereNull('snooze_until_date');
        }
        $feed = $feed->whereDate('display_from_date', '<=', Carbon::today()->toDateString());
        $feed = $feed->get();

        foreach ($feed as $key => $value) {
            $feed[$key]->adgroup;
            if($feed[$key]->adgroup->status!='enabled'){
              unset($feed[$key]);
              continue;
            }
            $feed[$key]->adgroup->campaign;
            $feed[$key]->performance = $feed[$key]->adgroup->performance->where("date_range", $feed[$key]->date_range)->first();
        }

        return $feed;
    }

    private function getQueryFeed($account, $include_snoozed)
    {
        $feed = SearchQueryFeed::where('account_id', $account->id)->take(10);
        if($include_snoozed){
            $feed = $feed->whereNotNull('snooze_until_date');
        }else{
            $feed = $feed->whereNull('snooze_until_date');
        }

        $feed = $feed->whereDate('display_from_date', '<=', Carbon::today()->toDateString())->orWhereNull('display_from_date');;
        $feed = $feed->get();

        foreach ($feed as $key => $value) {
            $feed[$key]->search_query;
            $feed[$key]->performance = $feed[$key]->search_query->performance->where("date_range", $feed[$key]->date_range)->first();

        }

        unset($feed->performance);
        
        return $feed;
    }

    private function getKeywordFeed($account, $include_snoozed)
    {
        $feed = KeywordFeed::where('account_id', $account->id)->take(10);
        if($include_snoozed){
            $feed = $feed->whereNotNull('snooze_until_date');
        }else{
            $feed = $feed->whereNull('snooze_until_date');
        }
        $feed = $feed->whereDate('display_from_date', '<=', Carbon::today()->toDateString());
        $feed = $feed->get();

        foreach ($feed as $key => $value) {
            $feed[$key]->keyword;
            if($feed[$key]->keyword->status!='enabled'){
              unset($feed[$key]);
              continue;
            }
            $performance = $feed[$key]->keyword->performance->where("date_range", $feed[$key]->date_range)->first();
            unset($feed[$key]->keyword->performance);
            $feed[$key]->performance = $performance;
            $feed[$key]->keyword->adgroup;
            $feed[$key]->keyword->adgroup->campaign;
        }
        // unset($feed->performance);
        return $feed;
    }

    private function getAdvertFeed($account, $include_snoozed)
    {

        // return AdvertFeed::whereNull('snooze_until_date')->where('account_id', $account->id)->get();

        $feed = AdvertFeed::where('account_id', $account->id)->take(10);
        if($include_snoozed){
            $feed = $feed->whereNotNull('snooze_until_date');
        }else{
            $feed = $feed->whereNull('snooze_until_date');
        }
        $feed = $feed->whereDate('display_from_date', '<=', Carbon::today()->toDateString());
        $feed = $feed->get();
        // return $feed;
        foreach ($feed as $key => $value) {

            $feed[$key]->advert;
            if($feed[$key]->advert->status!='enabled'){
              unset($feed[$key]);
              continue;
            }
            $feed[$key]["performance"] = $feed[$key]->advert->performance->where("date_range", $feed[$key]->date_range)->first();
            $feed[$key]->advert->adgroup;
            $feed[$key]->advert->adgroup->campaign;

        }

        return $feed;
    }

    public function index(\App\Models\Account $account)
    {
        return view('user.feed.show');
    }

    public function archiveFeedItem(Request $request, \App\Models\Account $account)
    {

        $table_name = $request->input('table_name');
        DB::table($table_name)
                            ->where('id', $request->input('id'))
                            ->update(['archived' => 1]);
    }

    public function snoozeFeedItem(Request $request, \App\Models\Account $account)
    {

        DB::table($request->input('table_name'))
                            ->where('id', $request->input('id'))
                            ->update(['snooze_until_date' => Carbon::now()->addWeek()]);
    }


    public function getEntityTableNameFeedTableName($table_name){
        if($table_name=="advert_feed"){
            return "adverts";
        }
    }

    public function getModelFromFeedTableName($table_name){
        if($table_name=="advert_feed"){
            return Advert;
        }
    }

}
