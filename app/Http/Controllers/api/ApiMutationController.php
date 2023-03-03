<?php

namespace App\Http\Controllers\api;

use App\Models\Advert;
use App\Models\Campaign;
use App\User;
use Auth;
use App\Http\Requests;
use App\Models\Account;
use App\Models\Mutation;
use Illuminate\Http\Request;
use App\Libraries\Breadcrumbs;
use App\Decorators\AccountDecorator;
use App\Http\Controllers\Controller;
use App\Notifications\KPITrackingReset;
use App\Notifications\BudgetTrackingReset;

class ApiMutationController extends Controller
{

    public function test(Account $account){
        return $account;
    }

    public function update(Request $request, Account $account)
    {

        logger($request);
        //TODO: set these to nullable
        $payload = $request['payload'];
        $empty_columns = ['value', 'destination_type', 'destination_google_id', 'response','attribute', 'entity_google_id', 'entity_id'];
        foreach($empty_columns as $column){
            if(array_key_exists($column,$payload) == false){
                $payload[$column] = '';
            }
        }        

        $mutation = new Mutation;
        $mutation->type = $payload['type']; 
        $mutation->destination_type = $payload['destination_type']; 
        $mutation->destination_google_id = $payload['destination_google_id']; 
        $mutation->response = $payload['response']; 
        $mutation->action = $payload['action']; 
        $mutation->attribute = $payload['attribute']; 
        $mutation->value = $payload['value']; 
        $mutation->entity_google_id = $payload['entity_google_id']; 
        $mutation->entity_id = $payload['entity_id']; 
        $mutation->account_id = $account->id; 
        // $mutation->created_at = $payload['created_at']; 
        // $mutation->updated_at = $payload['updated_at']; 

        $mutation->save();

    }
  
}
