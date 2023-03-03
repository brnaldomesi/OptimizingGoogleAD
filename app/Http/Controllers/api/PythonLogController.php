<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PythonLogController extends Controller
{
    public function index(Request $request)
    {
        $account_id = $request->input( 'account_id' );
        $search = $request->input( 'search' );
        $category = $request->input( 'category' );
        $collection = \App\Models\PythonLog::orderBy('created_at', 'DESC');
        if($account_id){
            $collection = $collection->where('account_id',$account_id);
        }
        if($search){
            $collection = $collection->where('message','like','%'.$search.'%')->orWhere('short_message','like','%'.$search.'%');
        }
        if($category){
            $collection = $collection->where('category',$category);
        }
        return collect($collection->get())->paginate(10);
    }
}
