<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class SinglePageController extends Controller
{
    public function index() {
        if(!\Auth::user()->refresh_token){
            return view('user.connect-adwords');
        }

        if (\Auth::user()->accounts->count() == 0) {
            return view('user.connect-adwords');
        }

        return view('user.app');
    }
}
