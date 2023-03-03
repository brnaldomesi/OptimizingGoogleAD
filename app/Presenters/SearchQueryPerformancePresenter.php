<?php

namespace App\Presenters;

use Akaunting\Money\Money;
use Akaunting\Money\Currency;
use Laracodes\Presenter\Presenter;

class SearchQueryPerformancePresenter extends Presenter
{
    public function ctr()
    {
        if (is_null($this->model->ctr)) {
            return '';
        }

        return $this->model->ctr.'%';
    }

    public function ctrSignificance()
    {
        if (is_null($this->model->ctr_significance)) {
            return '';
        }

        return $this->model->ctr_significance.'%';
    }

    public function cost()
    {
        if (is_null($this->model->cost)) {
            return '';
        }

        return Money::{$this->model->account->currency_code}($this->model->cost * 100);
    }

    public function averageCpc()
    {
        if (is_null($this->model->average_cpc)) {
            return '';
        }

        return Money::{$this->model->account->currency_code}($this->model->average_cpc * 100);
    }

    public function conversionValue()
    {
        if (is_null($this->model->conversion_value)) {
            return '';
        }

        return Money::{$this->model->account->currency_code}($this->model->conversion_value * 100);
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
