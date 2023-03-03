<?php

namespace App\Http\Controllers\api;

use App\Models\Advert;
use App\Models\Campaign;
use App\User;
use Auth;
use App\Http\Requests;
use App\Models\Account;
use Illuminate\Http\Request;
use App\Libraries\Breadcrumbs;
use App\Decorators\AccountDecorator;
use App\Http\Controllers\Controller;
use App\Notifications\KPITrackingReset;
use App\Notifications\BudgetTrackingReset;
use Akaunting\Money\Currency;
use App\Jobs\RunPythonAccountProcessing;
use \Firebase\JWT\JWT;

class AccountController extends Controller
{
    public function index()
    {
        //see comment to show method below for the reasons for this unusual approach
        $id = Auth::user()->current_account_id;
        $account = Account::find($id);

        if (is_null($account)) {
            $account = Account::where('user_id', Auth::id())->first();
        }

        return redirect()->action('User\AccountController@show', ['account'=>$account]);

    }

    public function accounts(\App\User $user){

        $accounts = \App\Models\Account::with("performance")
            ->with("budgetCommander")
            ->where("user_id", $user->id)
            ->where("user_id", $user->id)
            ->orderBy('budget','DESC')
            ->orderBy('name', 'ASC')
            ->get();
        
        $accounts = $accounts->map(function ($object) {

            $object->performance = $object->performance->where("date_range", 'last_30_days')->first();
            $object->currency = app('App\Http\Controllers\api\AccountController')->currency($object);
            return $object;
        
        });

        return $accounts;

    }

    public function numberOfSyncedAccounts(\App\User $user){

        return \App\Models\Account::where("user_id", $user->id)->where('is_synced', '1')->count();

    }

    public function currency(Account $account){

      return new Currency(($account->currency_code));

    }

    public function getSyncInfo(Account $account){
      return [
        'account_id' => $account->id,
        'is_synced' => $account->is_synced,
        'ad_performance_report_processed_at' => $account->ad_performance_report_processed_at
      ];
    }

    public function toggleIsSynced(Request $request, Account $account)
    {

        if($request->method()=="POST"){

            $account->is_synced = !$account->is_synced;
            $account->save();
      
            if($account->is_synced && !$account->ad_performance_report_processed_at){
              dispatch(new RunPythonAccountProcessing($account->id));
            }
        }

      return strval($account->is_synced);
    }


    public function show(Account $account = null)
    {
        if (is_null($account)) {
            $account = Account::where('user_id', Auth::id())->first();
        }

        //we'll use this to generate the sidebar
        Auth::user()->current_account_id = $account->id;
        Auth::user()->save();

        $breadcrumbs = new Breadcrumbs;

        $dateRange = Auth::user()->date_range;

        $accounts = Account::with(['performance' => function ($query) use ($dateRange) {
            $query->where('date_range', $dateRange);
        }])->where('id', $account->id)->get();

        $accounts = (new AccountDecorator($accounts))->get();

        return view('user.account.show', [
            'account'	=>	$accounts[0],
            'breadcrumbs' =>$breadcrumbs->get(),
        ]);
    }

    public function update(Request $request, Account $account)
    {

        //@todo: validate

        //just in case the user pops a percentage sign in there
        $request->merge(['kpi_value' => str_replace('%', '', $request->input('kpi_value'))]);

        $request->validate([
            'budget'  	=>  'numeric|min:0',
            'kpi_value'	=>	'numeric|min:0',
        ]);

        //reset budget performance fields if budget changes
        if ($account->budget != $request->input('budget')) {
            $account->budget_actual_vs_target = null;
            $account->budget_forecast_vs_target = null;

            Auth::user()->notify(new BudgetTrackingReset);
        }

        //reset kpi performance fields if kpi name or value change
        if ($account->kpi_name != $request->input('kpi_name') || $account->kpi_value != $request->input('kpi_value')) {
            $account->kpi_actual_vs_target = null;
            $account->kpi_forecast_vs_target = null;

            Auth::user()->notify(new KPITrackingReset);
        }

        $account->budget = $request->input('budget');
        $account->kpi_name = $request->input('kpi_name');
        $account->kpi_value = $request->input('kpi_value');

        $account->save();

        return redirect()->back();
    }

    public function getSsoToken(\App\User $user) 
    {
        $ssoToken = $this->createCannyToken($user);
        $boardToken = '9c8cab8e-5634-e7cf-cfba-60c6be93e9e9';
        return [
            'ssoToken' => $ssoToken,
            'boardToken' => $boardToken
        ];
    }

    public function createCannyToken($user) {
        $PrivateKey = '7dbb89e7-87d7-6232-e61e-143b3c50ed96';

        $userData = [
          'email' => $user['email'],
          'id' => $user['id'],
          'name' => $user['name'],
        ];
        return JWT::encode($userData, $PrivateKey, 'HS256');
    }
}
