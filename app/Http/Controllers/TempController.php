<?php

namespace App\Http\Controllers;

use Akaunting\Money\Money;
use Akaunting\Money\Currency;
use App\Http\Controllers\Controller;
use App\Libraries\AdWordsAPISession;

class TempController extends Controller
{
    public function index()
    {
        $currency = 'USD';
        echo Money::$currency(500);
    }
}
