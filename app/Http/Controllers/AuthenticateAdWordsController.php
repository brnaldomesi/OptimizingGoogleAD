<?php

namespace App\Http\Controllers;

use Log;
use Auth;
use App\User;
use Socialite;
use App\Models\Account;
use App\Models\PythonQueue;
use App\Libraries\AccountFactory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Jobs\RunPythonEveryTimeUserRegisters;


class AuthenticateAdWordsController extends Controller
{
    /**
     * this handle's Google's callback.
     *
     * @return void
     * @author
     **/
    public function __invoke()
    {
        Log::info("Handling google's callback");

        $user = Auth::user();

        $apiUser = Socialite::driver('google')->user();

        $user->google_image_url = $apiUser->avatar;
        $user->refresh_token = $apiUser->refreshToken;
        
        $user->save();

        //first run
        if ($user->accounts->isEmpty()) {
    
            dispatch(new RunPythonEveryTimeUserRegisters);
            
        } else {
            
            Log::info("The account already has data");
        }
        
        Log::info('Returning waiting view...');
        return view('user.waiting-for-data');

    }
}
