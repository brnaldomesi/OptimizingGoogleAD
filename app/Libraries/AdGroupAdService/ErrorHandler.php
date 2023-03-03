<?php

namespace App\Libraries\AdGroupAdService;

use App\Libraries\FindAdvertFromAdGroupAd;

class ErrorHandler
{
    public $errors = [];

    protected $user;

    protected $adverts;

    protected $operations;

    protected $failedOperations = [];

    protected $advertsWithErrors;

    public function __construct($user, $adverts, $operations)
    {
        $this->user = $user;
        $this->adverts = $adverts;
        $this->operations = $operations;

        self::getFailedOperations();

        $this->advertsWithErrors = collect([]);
        self::getAdvertsWithErrors();
    }

    public function handle()
    {
    }

    //from https://github.com/googleads/googleads-php-lib/blob/master/examples/AdWords/v201802/ErrorHandling/HandlePolicyViolationError.php
    protected function getFailedOperations()
    {
        foreach ($this->errors as $error) {

            // Get the index of the failed operation from the error's field path elements.
            $fieldPathElements = $error->getFieldPathElements();

            $firstFieldPathElement = null;

            if ($fieldPathElements !== null && count($fieldPathElements) > 0) {
                $firstFieldPathElement = $fieldPathElements[0];
            }

            if ($firstFieldPathElement !== null
                    && $firstFieldPathElement->getField() == 'operations'
                    && $firstFieldPathElement->getIndex() !== null) {
                $operationIndex = $firstFieldPathElement->getIndex();

                $this->failedOperations[] = $this->operations[$operationIndex];
            }
        }
    }

    protected function getAdvertsWithErrors()
    {
        if (empty($this->failedOperations)) {
            $this->advertsWithErrors = $this->adverts;
        } else {
            foreach ($this->failedOperations as $operation) {
                if ($advert = (new FindAdvertFromAdGroupAd($this->adverts, $operation->getOperand()->getAd()))->get()) {
                    $this->advertsWithErrors->push($advert);
                }
            }
        }
    }
}
