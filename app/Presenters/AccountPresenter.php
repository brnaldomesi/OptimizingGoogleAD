<?php

namespace App\Presenters;

use Akaunting\Money\Money;
use Akaunting\Money\Currency;
use App\Presenters\FormatMessage;
use Laracodes\Presenter\Presenter;

class AccountPresenter extends Presenter
{
    public function budget()
    {
        if (is_null($this->model->budget)) {
            return '';
        }

        return Money::{$this->model->currency_code}($this->model->budget * 100);
    }
}
