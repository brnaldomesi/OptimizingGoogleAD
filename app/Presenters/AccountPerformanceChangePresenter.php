<?php

namespace App\Presenters;

use Akaunting\Money\Money;
use Akaunting\Money\Currency;
use Laracodes\Presenter\Presenter;

class AccountPerformanceChangePresenter extends Presenter
{
    public function ctr()
    {
        if (is_null($this->model->ctr)) {
            return '';
        }

        return $this->model->ctr.'%';
    }

    public function ctrBaseline()
    {
        if (is_null($this->model->ctr_baseline)) {
            return '';
        }

        return $this->model->ctr_baseline.'%';
    }

    public function conversionRate()
    {
        if (is_null($this->model->conversion_rate)) {
            return '';
        }

        return $this->model->conversion_rate.'%';
    }

    public function conversionRateBaseline()
    {
        if (is_null($this->model->conversion_rate_baseline)) {
            return '';
        }

        return $this->model->conversion_rate_baseline.'%';
    }

    public function cpa()
    {
        if (is_null($this->model->cpa)) {
            return '';
        }

        return Money::{$this->model->account->currency_code}($this->model->cpa * 100);
    }

    public function cpaBaseline()
    {
        if (is_null($this->model->cpa_baseline)) {
            return '';
        }

        return Money::{$this->model->account->currency_code}($this->model->cpa_baseline * 100);
    }

    public function roas()
    {
        if (is_null($this->model->roas)) {
            return '';
        }

        return $this->model->roas.'%';
    }

    public function roasBaseline()
    {
        if (is_null($this->model->roas_baseline)) {
            return '';
        }

        return $this->model->roas_baseline.'%';
    }

    public function ctrGraphDataCurrent()
    {
        if (is_null($this->model->ctr_graph_data)) {
            return '[]';
        }

        $values = implode(',', $this->model->ctr_graph_data['current']);

        return '['.$values.']';
    }

    public function ctrGraphDataPrevious()
    {
        if (is_null($this->model->ctr_graph_data)) {
            return '[]';
        }

        $values = implode(',', $this->model->ctr_graph_data['previous']);

        return '['.$values.']';
    }

    public function conversionRateGraphDataCurrent()
    {
        if (is_null($this->model->conversion_rate_graph_data)) {
            return '[]';
        }

        $values = implode(',', $this->model->conversion_rate_graph_data['current']);

        return '['.$values.']';
    }

    public function conversionRateGraphDataPrevious()
    {
        if (is_null($this->model->conversion_rate_graph_data)) {
            return '[]';
        }

        $values = implode(',', $this->model->conversion_rate_graph_data['previous']);

        return '['.$values.']';
    }

    public function cpaGraphDataCurrent()
    {
        if (is_null($this->model->cpa_graph_data)) {
            return '[]';
        }

        $values = implode(',', $this->model->cpa_graph_data['current']);

        return '['.$values.']';
    }

    public function cpaGraphDataPrevious()
    {
        if (is_null($this->model->cpa_graph_data)) {
            return '[]';
        }

        $values = implode(',', $this->model->cpa_graph_data['previous']);

        return '['.$values.']';
    }

    public function roasGraphDataCurrent()
    {
        if (is_null($this->model->roas_graph_data)) {
            return '[]';
        }

        $values = implode(',', $this->model->roas_graph_data['current']);

        return '['.$values.']';
    }

    public function roasGraphDataPrevious()
    {
        if (is_null($this->model->roas_graph_data)) {
            return '[]';
        }

        $values = implode(',', $this->model->roas_graph_data['previous']);

        return '['.$values.']';
    }
}
