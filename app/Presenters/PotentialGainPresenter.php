<?php

namespace App\Presenters;

use Akaunting\Money\Money;
use Akaunting\Money\Currency;
use Laracodes\Presenter\Presenter;

class PotentialGainPresenter extends Presenter
{
    public function cpa()
    {
        if (is_null($this->model->cpa)) {
            return '';
        }

        return Money::{$this->model->account->currency_code}($this->model->cpa * 100);
    }

    public function costChange()
    {
        if (is_null($this->model->cost_change)) {
            return '';
        }

        return Money::{$this->model->account->currency_code}($this->model->cost_change * 100);
    }
}
