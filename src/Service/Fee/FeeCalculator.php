<?php

namespace Lendable\Interview\Interpolation\Service\Fee;

use Lendable\Interview\Interpolation\Repository\FeeRepository;
use Lendable\Interview\Interpolation\Model\LoanApplication;
use Exception;

class FeeCalculator implements FeeCalculatorInterface, LoanInterface {

    /**
     * @var FeeRepository
     */
    protected $feeRepository;

    /**
     * @var string
     */
    protected $currencyShort = 'GBP';
    
    /**
     * @var float
     */
    protected $loanMinimum = 1000.00;
    
    /**
     * @var float
     */
    protected $loanMaximum = 20000.00;

    /**
     *  Inject fee repository.
     * @param FeeRepository $feeRepository
     */
    public function __construct(FeeRepository $feeRepository) {
        $this->feeRepository = $feeRepository;
    }

    /**
     * Set the currency short.
     * @param string $currencyShort
     */
    public function setCurrencyShort(string $currencyShort) {
        $this->currencyShort = $currencyShort;
    }

    /**
     * Set the calculators loan minimum.
     * @param type $loanMinimum
     */
    public function setLoanMinimum(float $loanMinimum) {
        $this->loanMinimum = $loanMinimum;
    }

    /**
     * Set the calculators loan maximum.
     * @param float $loanMaximun
     */
    public function setLoanMaximum(float $loanMaximun) {
        $this->loanMaximum = $loanMaximun;
    }

    /**
     *  Calculate the application fee.
     *  Exception handling i.e. ErrorException: Division by zero return maximum.
     *  If upper and lower, calculate.
     *  If upper and no lower, take upper.
     *  If no upper and no lower OR only lower, return zero fee.
     * @param LoanApplication $application
     * @return float
     */
    public function calculate(LoanApplication $application): float {
        $applicatonAmount = $this->validateLoanRange($application->getAmount());

        $lowerFee = $this->feeRepository->find($applicatonAmount, $application->getTerm(), $this->currencyShort, false);
        $upperFee = $this->feeRepository->find($applicatonAmount, $application->getTerm(), $this->currencyShort);

        if ($upperFee && $lowerFee) {
            try {
                return $this->roundUp((($applicatonAmount - $lowerFee->getLoanAmount()) * ( $upperFee->getAmount() - $lowerFee->getAmount()) / ( $upperFee->getLoanAmount() - $lowerFee->getLoanAmount())) + $lowerFee->getAmount());
            } catch (Exception $exception) {
                return max($upperFee->getAmount(), $lowerFee->getAmount());
            }
        } elseif ($upperFee) {
            return $upperFee->getAmount();
        } else {
            return 0.00;
        }
    }

    /**
     * Validate that the application loan amount is between the restrictions before hitting a data source.
     * If application amount is lower than the minimum, then set application amount to set minimum.
     * If application amount is greater than the maximum, then set application amount to the set maximum.
     * @param float $applicationAmount
     * @return type
     */
    protected function validateLoanRange(float $applicationAmount) : float {
        if ($applicationAmount < $this->loanMinimum) {
            $loanAmount = $this->loanMinimum;
        } elseif ($applicationAmount > $this->loanMaximum) {
            $loanAmount = $this->loanMaximum;
        }
        return $this->roundup($loanAmount ?? $applicationAmount);
    }

    /**
     * Round up decimal places.
     * Check if multiple of 5 default.
     * If not, round up to nearest multiple (of 5 default).
     * @param type $value
     * @param type $multipleOf
     * @return float
     */
    protected function roundUp($value, $multipleOf = 5): float {
        $value = ceil($value);
        if ($value % $multipleOf == 0) {
            return round($value);
        }
        return round(($value + $multipleOf / 2) / $multipleOf) * $multipleOf;
    }

}
