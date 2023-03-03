<?php

namespace App\Http\Controllers\User;

use Auth;
use App\Models\SearchQuery;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SearchQueryPerformance;
use App\Libraries\HumanReadibleDateRange;

class SearchQueryPerformanceController extends Controller
{
    public function __invoke(\App\Models\Account $account)
    {
        $dateRange = Auth::user()->date_range;

        $output = [];
        $output['dateRange'] = (new HumanReadibleDateRange($dateRange))->get();

        $output['searchQueryPerformance'] = SearchQueryPerformance::where('account_id', $account->id)->where('date_range', $dateRange)->get();

        foreach ($output['searchQueryPerformance'] as $key=>$collection) {
            $query = $collection->search_query->query;
            $output['searchQueryPerformance'][$key]['query'] = $query;
        }

        //format data for graph

        $records = SearchQueryPerformance::where('account_id', $account->id)->where('date_range', $dateRange)->where('show_on_graph', true)->orderBy('graph_order')->get();

        foreach ($records as $key=>$collection) {
            $query = $collection->search_query->query;
            $records[$key]['query'] = $query;
        }

        $output['graphValues'] = $this->graphValues($records);

        return view('user.searchqueryperformance.show', $output);
    }

    public function api(\App\Models\Account $account)
    {
        $dateRange = Auth::user()->date_range;

        $output = [];

        $output['searchQueryPerformance'] = SearchQueryPerformance::where('account_id', $account->id)->get();

        return $output;
    }

    //formats data to be used in the graph
    protected function graphValues($records)
    {

        //doign this the clunky way because the collection toJson method doesn't appear to allow JSON_FORCE_OBJECT

        $return = [];

        foreach ($records as $record) {
            $a = [
                'CTR' 			=>	$record->ctr,
                'Conv. Rate'	=>	$record->conversion_rate,
                'ROAS'			=>	$record->roas,
            ];

            $return[$record->query] = json_encode($a, JSON_FORCE_OBJECT);
        }

        return json_encode($return, JSON_FORCE_OBJECT);
    }
}
