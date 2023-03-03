<?php

namespace App\Http\Controllers\api;

use App\Models\Account;
use App\Http\Resources\AccountBudgetResource;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountBudgetController extends Controller
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
     * Display the specified resource.
     *
     * @param Account $account
     * @return AccountBudgetResource
     */
    public function show(Account $account)
    {
        return new AccountBudgetResource($account);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Account $account
     * @return AccountBudgetResource
     */
    public function update(Request $request, Account $account)
    {
        $account->budget = $request->budget;
        $account->save();

        return new AccountBudgetResource($account);
    }

}
