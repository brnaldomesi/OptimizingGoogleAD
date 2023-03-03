<?php

namespace App\Libraries\CustomerService;

class UpdateAccountsWithDataFromGoogle
{
    //@todo:handle errors/exceptions here
    protected $accounts; //collection of accounts

    public function __construct($accounts)
    {
        $this->accounts = $accounts;
    }

    public function handle($response)
    {
        foreach ($response as $customer) {
            if ($account = $this->findAccountByGoogleId($customer->getCustomerId())) {
                $account->name = $customer->GetDescriptiveName();
                $account->google_id = strval($customer->getCustomerId());
                $account->currency_code = $customer->getCurrencyCode();
                $account->timezone = $customer->getDateTimeZone();

                $account->save();
            }
        }
    }

    protected function findAccountByGoogleId($googleId)
    {
        return $this->accounts->where('google_id', $googleId)->first();
    }
}
