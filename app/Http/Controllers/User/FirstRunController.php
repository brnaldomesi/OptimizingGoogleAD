<?php

namespace App\Http\Controllers\User;

use Auth;
use App\Models\Account;
use App\Http\Controllers\Controller;
use App\Libraries\AdWordsAPISession;

class FirstRunController extends Controller
{
    //waiting for data screen polls this
    public function firstReportProcessed()
    {
        //no accounts yet - waiting for first call to google to create the account for the user
        if (Auth::user()->accounts->count() == 0) {
            return 'false';
        }

        //got an account but the report hasn't been procesed
        if (! Auth::user()->accounts[0]->ad_performance_report_processed_at) {
            return 'false';
        }

        return 'true'; //report processed, show the dashboard
    }

      //waiting for data screen polls this
    public function accountsDownloaded()
    {

          if (Auth::user()->accounts->count() == 0) {
              return 'false';
          } else{
              //return to the accounts page
              return 'true';
          }

  
      }

    
}
