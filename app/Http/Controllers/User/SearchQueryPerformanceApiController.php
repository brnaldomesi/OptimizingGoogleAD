<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SearchQueryPerformance;
use App\Libraries\HumanReadibleDateRange;

class SearchQueryPerformanceApiController extends Controller
{
    public function index()
    {
        $output['this'] = 'that';
    }
}
