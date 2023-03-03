<?php

namespace App\Http\Controllers\User;

use Auth;
use Illuminate\Http\Request;
use App\Models\AdNGramPerformance;
use App\Http\Controllers\Controller;
use App\Libraries\HumanReadibleDateRange;

class AdNGramPerformanceController extends Controller
{
    public function __invoke(\App\Models\Account $account)
    {
        $dateRange = Auth::user()->date_range;

        $output = [];
        $output['dateRange'] = (new HumanReadibleDateRange($dateRange))->get();

        $output['nGramPerformance'] = AdNGramPerformance::where('account_id', $account->id)->where('date_range', $dateRange)->get();

        //format data for graph

        $records = AdNGramPerformance::where('account_id', $account->id)->where('date_range', $dateRange)->where('show_on_graph', true)->orderBy('graph_order')->get();

        $output['graphValues'] = $this->graphValues($records);

        return view('user.ngramperformance.show', $output);
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

            $return[$record->n_gram] = json_encode($a, JSON_FORCE_OBJECT);
        }

        return json_encode($return, JSON_FORCE_OBJECT);
    }
}
