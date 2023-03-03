<?php

namespace App\Http\Controllers\User;

use Auth;
use Socialite;
use App\Models\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConnectAdWordsController extends Controller
{
    public function __invoke(Request $request)
    {
        return Socialite::driver('google')
        ->scopes(['https://www.googleapis.com/auth/adwords'])
        ->with([
            'redirect_uri' => env('GOOGLE_REDIRECT', \URL::to('/').'/handle-authentication'),
            'access_type' => 'offline',
            'prompt' => 'consent',
            ])
        ->redirect();
    }
}
