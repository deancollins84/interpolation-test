<?php

namespace Lendable\Interview\Interpolation\Model;

final class Fee {

    /**
     * @var string
     */
    private $currency;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var float
     */
    private $loanAmount;

    /**
     * 
     * @param int $amount
     * @param int $loanAmount
     * @param int $term
     * @param string $currency
     */
    public function __construct(int $amount, int $loanAmount, string $currency = 'GBP') {
        $this->amount = $amount;
        $this->loanAmount = $loanAmount;
        $this->currency = $currency;
    }

    /**
     * Get the fee amount.
     * @return float
     */
    public function getAmount() {
        return $this->amount;
    }

    /**
     * Get the corresponding loan amount. 
     * @return float
     */
    public function getLoanAmount() {
        return $this->loanAmount;
    }

    /**
     * Currency short.
     * @return string
     */
    public function getCurrency() {
        return $this->currency;
    }

}
