<?php

namespace App\Http\Controllers\api;

use Log;
// use DB;
use Illuminate\Support\Facades\DB;
use Auth;
use Carbon\Carbon;
use App\Models\Adgroup;
use App\Models\Campaign;
use App\Models\AdNGramPerformance;
use App\Models\AccountWinningElement;
use App\Models\CampaignWinningElement;
use App\Models\AdgroupWinningElement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdTestController extends Controller
{

    public $date_range;

    public function getDateRange($adverts){

      if(isset($this->date_range))$this->date_range;

      foreach($adverts as $advert){
        $all_performance = $advert->performance;
        // dd($all_performance->where('date_range', 'last_112_days'));
        foreach($all_performance as $performance){

          if($performance['conversion_rate_message']=='winning'){
            return $performance['date_range'];
          }
          if($performance['conversion_rate_message']=='losing'){
            return $performance['date_range'];
          }
          if($performance['ctr_message']=='winning'){
            return $performance['date_range'];
          }
          if($performance['ctr_message']=='losing'){
            return $performance['date_range'];
          }
        }
       
        
      }
      
      return 'last_30_days';

    }

    public function performanceMap($object){
      $performance = $object->performance;              
      $performance = $object->performance->where('date_range', $this->date_range);
      unset($object->performance);
      $object->performance = $performance;
      return $object;
    }

    private function getAdverts($adgroup, $account, $ad_type){

      $adverts =  $adgroup->adverts->whereIn('status', ['paused','enabled'])
            ->where('ad_type',$ad_type)
            ->where('account_id', $account->id)
            ->sortBy('status')
            ->sortByDesc('clicks')
            ->take(10);

        $this->date_range = $this->getDateRange($adverts);

        $adverts = $adverts->map(function ($object) {
          return $this->performanceMap($object);
        });
        
        return $adverts;
    }

    private static function addKeywords(AdGroup $adgroup){
      return $adgroup->keywords()->where('status', 'enabled')->get(['keyword_text','keyword_match_type']);
    
    } 

    public function adgroup(\App\Models\Account $account)
    {
        $data = Adgroup::where('account_id', $account->id)
        ->where('priority', '>', 0)
        ->where('status', 'enabled')
        ->orderBy('priority', 'desc')->get();
        
        foreach($data as $adgroup){
            $adgroup->campaign->name;
            $adgroup->keywords = self::addKeywords($adgroup);
            
            if($adgroup->ad_count==0){
                $adgroup['url'] = '';
                $adgroup['path_1'] = '';
                $adgroup['path_2'] = '';
                continue;
            }

            $expanded_text_ads = $this->getAdverts($adgroup, $account, 'Expanded text ad');
            $responsive_search_ads = $this->getAdverts($adgroup, $account, 'Responsive search ad');

            $adgroup['eta_ad_count'] = $expanded_text_ads->count();
            $adgroup['rsa_ad_count'] = $responsive_search_ads->count();
            
            unset($adgroup['adverts']);
            $adgroup['expanded_text_ads'] = $expanded_text_ads->all();
            $adgroup['responsive_search_ads'] = $responsive_search_ads->all();

            //this is for the placeholder text on the advert creator
            $advert = $expanded_text_ads->first();
            if($advert==null)$advert = $responsive_search_ads->first();
            if($advert==null)continue;

            $adgroup['url'] = $advert->final_urls[0];
            $adgroup['path_1'] = $advert->path_1;
            $adgroup['path_2'] = $advert->path_2;
            $adgroup['headline_1'] = $advert->headline_1;
            $adgroup['headline_2'] = $advert->headline_2;
            $adgroup['headline_3'] = $advert->headline_3;
            $adgroup['description'] = $advert->description;
            $adgroup['description_2'] = $advert->description_2;


        }

        // $data = $data->filter(function ($value, $key){
        //   return $value['expanded_text_ads']!=null || $value['eta_ad_count'] > 1;
        // });

        return collect($data)->paginate(1);
    }

    public function winningElements(\App\Models\Account $account, $model, $type)
    {

        $winning_elements = $model::where('account_id', $account->id)->where('value', '!=', '')
        ->where('type', 'like', '%'.$type.'%')
        ->get();
        return $winning_elements;
    
    }

    public function nGrams($account){
      return AdNGramPerformance::where('account_id', $account->id)
      ->where('date_range', 'last_56_days');
    }

    public function adgroupNGrams(\App\Models\Account $account, AdGroup $adgroup)
    {
          $ad_test = new AdTestController;
          return $ad_test::nGrams($account, $adgroup)->where('adgroup_id', $adgroup->id)
          ->orderBy('clicks', 'desc')
          ->pluck('n_gram')
          ->take(5)
          ->toArray();
    }

    public function campaignNGrams(\App\Models\Account $account, Campaign $campaign)
    {
          $ad_test = new AdTestController;
          return $ad_test::nGrams($account)->where('campaign_id', $campaign->id)
          ->orderBy('clicks', 'desc')
          ->pluck('n_gram')
          ->take(5)
          ->toArray();
    }

    public function accountNGrams(\App\Models\Account $account)
    {
          $ad_test = new AdTestController;
          return $ad_test::nGrams($account)
          ->orderBy('clicks', 'desc')
          ->pluck('n_gram')
          ->take(5)
          ->toArray();
    }

    public function accountHeadlines(\App\Models\Account $account)
    {
        $adtest = new AdTestController;
        return collect($adtest::winningElements($account, AccountWinningElement::class, 'headline'))
        ->pluck('value')
        ->take(5)
        ->toArray();
    }

    public function campaignHeadlines(\App\Models\Account $account, Campaign $campaign)
    {
        $adtest = new AdTestController;
        return $adtest::winningElements($account, CampaignWinningElement::class, 'headline')
        ->where('campaign_id', $campaign->id)
        ->pluck('value')
        ->take(5)
        ->toArray();
    }

    public function adgroupHeadlines(\App\Models\Account $account, AdGroup $adgroup)
    {
        $adtest = new AdTestController;
        return collect($adtest::winningElements($account, AdgroupWinningElement::class, 'headline'))
        ->where('adgroup_id', $adgroup->id)
        ->pluck('value')
        ->take(5)
        ->toArray();
    }

    public function accountDescriptions(\App\Models\Account $account)
    {

      $adtest = new AdTestController;
      return collect($adtest::winningElements($account, AccountWinningElement::class, 'description'))
      ->pluck('value')
      ->take(5)
      ->toArray();
      
    }

    public function campaignDescriptions(\App\Models\Account $account, Campaign $campaign)
    {
        $adtest = new AdTestController;
        return collect($adtest::winningElements($account, CampaignWinningElement::class, 'description'))
        ->where('campaign_id', $campaign->id)
        ->pluck('value')
        ->take(5)
        ->toArray();
    }

    public function adgroupDescriptions(\App\Models\Account $account, AdGroup $adgroup)
    {
        $adtest = new AdTestController;
        return collect($adtest::winningElements($account, AdgroupWinningElement::class, 'description'))
        ->where('adgroup_id', $adgroup->id)
        ->pluck('value')
        ->take(5)
        ->toArray();
    }
}
