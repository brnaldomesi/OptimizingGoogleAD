<?php

namespace App\Http\Controllers\User;

use App\Models\Plan;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Subscription;

class SubscriptionsController extends Controller
{

    public function index()
    {
        // ToDo: offer change of plans
        return $this->display_subscriptions_page();
    }

    public function payment(Request $request)
    {
        $account = User::find(Auth::user()->id);

        $plan = (new Plan)->getPlan($request->currency, $request->accounts);

        return view('user.subscriptions.payment',[
            'intent' => $account->createSetupIntent(),
            'plan' => $plan,
        ]);
    }

    public function add_subscription(Request $request)
    {
        $account = User::find(Auth::user()->id);
        $email = $account['email'];

        // Subscription (product)
        $currentSubscription = $this->currentSubscription();
        $currentPlanId = $currentSubscription['plan']['id'];
        $newPlanId = $request['plan'];
        $newPlan = Plan::find($newPlanId);
        $paymentMethod = $request['payment_method'];

        if(!$currentPlanId){
            // Create new subscription
            $account->newSubscription($newPlan['stripe_prod'], $newPlan['stripe_plan'])->create($paymentMethod, [
                'name' => $account['name'],
                'email' => $email
            ]);

            // Set currency as Stripe does not allow a customer to change their initial currency
            $account->currency = $newPlan['currency'];
            $account->save();

        }elseif($currentPlanId != $newPlanId){
            // Swap plans
            Log::info('Swap '
                . $currentSubscription['name'] . ' to '
                . $newPlan['stripe_plan'] . ' on '
                . $account['name']);

            $account->subscription($currentSubscription['name'])
                ->swap($newPlan['stripe_plan']);

            DB::table('subscriptions')->where('id', $currentSubscription['id'])->update(['name' => $newPlan['stripe_prod']]);

        }
        return $this->display_subscriptions_page();
    }

    public function display_subscriptions_page()
    {
        $account = User::find(Auth::user()->id);
        $subscription = Subscription::where('user_id', $account['id'])
                                    ->where('stripe_status', 'active')
                                    ->orderBy('created_at', 'desc')->first();

        $plans = Plan::where('active', 1)->get();
        $plan = Plan::where('stripe_plan', $subscription['stripe_plan'])->first();

        // Data for trial plan
        if($plan === null)
        {
            $plan = new Plan;
            $plan->name = 'trial';
            $plan->currency = 'USD';
            $plan->price = 0;
            $plan->user_limit = 0;
            $plan->account_limit = 0;
            $plan->frequency = 'trial account';
        }

        return view('user.subscriptions.index')
                ->with('subscription', $subscription)
                ->with('plans', $plans)
                ->with('plan', $plan)
                ->with('account', $account);
    }

    public function currentSubscription()
    {
        // Lists latest subscription (ignore old subscriptions as we will only allow one.
        $account = User::find(Auth::user()->id);
        $subscription = $account->subscriptions->first();

        if($subscription){
            $plan = Plan::where('stripe_plan', $subscription->stripe_plan)->get()->toArray();

            // Add plan data to the subscription
            $subscription['plan'] = $plan[0];
        }else{
            $subscription = ['plan'] == 'Not Subscribed';
        }

        return $subscription;
    }

}
