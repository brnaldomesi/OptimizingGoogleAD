<?php

namespace App\Http\Controllers\api;

use App\Http\Resources\AccountKpiResource;
use App\Models\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountKpiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Account $account
     * @return AccountKpiResource
     */
    public function show(Account $account)
    {
        return new AccountKpiResource($account);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Account $account
     * @return AccountKpiResource
     */
    public function update(Request $request, Account $account)
    {
        // Updates Target KPI, KPI value
        if($request['kpi_value']) $account->kpi_value = $request['kpi_value'];
        if($request['kpi_name']) $account->kpi_name = $request['kpi_name'];
        $account->save();

        // return new AccountKpiResource($account);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}


