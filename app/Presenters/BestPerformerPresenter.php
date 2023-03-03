<?php

namespace App\Presenters;

use Akaunting\Money\Money;
use Akaunting\Money\Currency;
use Laracodes\Presenter\Presenter;

class BestPerformerPresenter extends Presenter
{
    public function ctr()
    {
        if (is_null($this->model->ctr)) {
            return '';
        }

        return $this->model->ctr.'%';
    }

    public function cpa()
    {
        if (is_null($this->model->cpa)) {
            return '';
        }

        return Money::{$this->model->account->currency_code}($this->model->cpa * 100);
    }

    public function roas()
    {
        if (is_null($this->model->roas)) {
            return '';
        }

        return $this->model->roas.'%';
    }

    public function conversionRate()
    {
        if (is_null($this->model->conversion_rate)) {
            return '';
        }

        return $this->model->conversion_rate.'%';
    }
}
