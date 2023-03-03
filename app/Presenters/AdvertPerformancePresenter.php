<?php

namespace App\Presenters;

use Akaunting\Money\Money;
use Akaunting\Money\Currency;
use App\Presenters\FormatMessage;
use Laracodes\Presenter\Presenter;

class AdvertPerformancePresenter extends Presenter
{
    public function cost()
    {
        if (is_null($this->model->cost)) {
            return '';
        }

        return Money::{$this->model->advert->adgroup->campaign->account->currency_code}($this->model->cost * 100);
    }

    public function averageCpc()
    {
        if (is_null($this->model->average_cpc)) {
            return '';
        }

        return Money::{$this->model->advert->adgroup->campaign->account->currency_code}($this->model->average_cpc * 100);
    }

    public function conversionValue()
    {
        if (is_null($this->model->conversion_value)) {
            return '';
        }

        return Money::{$this->model->advert->adgroup->campaign->account->currency_code}($this->model->conversion_value * 100);
    }

    public function cpa()
    {
        if (is_null($this->model->cpa)) {
            return '';
        }

        return Money::{$this->model->advert->adgroup->campaign->account->currency_code}($this->model->cpa * 100);
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

    public function ctr()
    {
        if (is_null($this->model->ctr)) {
            return '';
        }

        return $this->model->ctr.'%';
    }

    public function message()
    {
        if (is_null($this->model->message)) {
            return '';
        }

        return (new FormatMessage($this->model->message))->get();
    }

    public function conversionRateSignificance()
    {
        if (is_null($this->model->conversion_rate_significance)) {
            return '';
        }

        return $this->model->conversion_rate_significance.'%';
    }

    public function conversionRateMessage()
    {
        if (is_null($this->model->conversion_rate_message)) {
            return '';
        }

        return (new FormatMessage($this->model->conversion_rate_message))->get();
    }

    public function ctrSignificance()
    {
        if (is_null($this->model->ctr_significance)) {
            return '';
        }

        return $this->model->ctr_significance.'%';
    }

    public function ctrMessage()
    {
        if (is_null($this->model->ctr_message)) {
            return '';
        }

        return (new FormatMessage($this->model->ctr_message))->get();
    }
}
