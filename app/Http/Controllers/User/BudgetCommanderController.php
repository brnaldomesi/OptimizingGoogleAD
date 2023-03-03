<?php

namespace App\Http\Controllers\User;

use DB;
use Auth;
use Carbon\Carbon;
use App\Models\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BudgetCommanderController extends Controller
{
    public function index(Account $account)
    {
        return view('user.budget-commander.show')->with(['account' => $account]);
    }

    public function api(Account $account)
    {
        $data = Account::select('budget_actual_vs_target',
                'budget_forecast_vs_target',
                'budget_target_graph_data',
                'budget_actual_graph_data',
                'currency_code',
                'budget'
                )->where('id', $account->id);
        $data = $data->get();
        dd($data);
        return $data;
    }
}
