<?php

use PHPUnit\Framework\TestCase;
use Lendable\Interview\Interpolation\Model\Fee;
use Lendable\Interview\Interpolation\Service\Fee\FeeCalculator;
use Lendable\Interview\Interpolation\Repository\FeeRepository;
use Lendable\Interview\Interpolation\Model\LoanApplication;

class FeeCalculatorUnitTest extends TestCase {

    protected $feeCalculator;

    /**
     * Stub fee repository.
     */
    public function setup() {
        $feeRepositoryStub = $this->createMock(FeeRepository::class);
        $feeRepositoryStub->method('find')->will(
                $this->onConsecutiveCalls(new Fee(100.00, 2000.00), new Fee(120.00, 3000.00))
        );
        $this->feeCalculator = new FeeCalculator($feeRepositoryStub);
    }

    /**
     * Example documented in readme.
     */
    public function testDocumentedExample() {
        $loanApplicationStub = $this->createMock(LoanApplication::class);
        $loanApplicationStub->method('getTerm')->will($this->returnValue(24));
        $loanApplicationStub->method('getAmount')->will($this->returnValue(2750.00));

        $fee = $this->feeCalculator->calculate($loanApplicationStub);
        $this->assertInternalType('float', $fee);
        $this->assertEquals(115.00, $fee);
    }

    /**
     * Application loan amount matches the upper range returned.
     */
    public function testUpperRangeFeeIsReturned() {
        $loanApplicationStub = $this->createMock(LoanApplication::class);
        $loanApplicationStub->method('getTerm')->will($this->returnValue(24));
        $loanApplicationStub->method('getAmount')->will($this->returnValue(3000.00));

        $fee = $this->feeCalculator->calculate($loanApplicationStub);
        $this->assertInternalType('float', $fee);
        $this->assertEquals(120.00, $fee);
    }

    /**
     * Application loan amount matches the lower range returned.
     */
    public function testLowerRangeFeeIsReturned() {
        $loanApplicationStub = $this->createMock(LoanApplication::class);
        $loanApplicationStub->method('getTerm')->will($this->returnValue(24));
        $loanApplicationStub->method('getAmount')->will($this->returnValue(2000.00));

        $fee = $this->feeCalculator->calculate($loanApplicationStub);
        $this->assertInternalType('float', $fee);
        $this->assertEquals(100.00, $fee);
    }

    /**
     * Application loan amount is a multiple of 5 to begin with.
     */
    public function testRoundUpFeeToMultipleOfFiveWhenAlreadyMultipleOfFive() {
        $fee = $this->feeCalculator->calculate(new LoanApplication(12, 2500));
        $this->assertInternalType('float', $fee);
        $this->assertEquals(110.00, $fee);
    }

    /**
     * Application loan amount is not a multiple of 5 to begin with.
     */
    public function testRoundUpFeeToMultipleOfFive() {
        $loanApplicationStub = $this->createMock(LoanApplication::class);
        $loanApplicationStub->method('getTerm')->will($this->returnValue(24));
        $loanApplicationStub->method('getAmount')->will($this->returnValue(2009.88));

        $fee = $this->feeCalculator->calculate($loanApplicationStub);
        $this->assertInternalType('float', $fee);
        $this->assertEquals(105.00, $fee);
    }

    /**
     * If the application loan amount is lower than set minimum, set the application loan amount to match and proceed. 
     */
    public function testApplicationAmountLowerThanSetMinimum() {
        $feeRepositoryStub = $this->createMock(FeeRepository::class);
        $feeRepositoryStub->method('find')->will(
                $this->onConsecutiveCalls(new Fee(70.00, 1000.00), new Fee(100.00, 2000.00))
        );
        $feeCalculator = new FeeCalculator($feeRepositoryStub);

        $loanApplicationStub = $this->createMock(LoanApplication::class);
        $loanApplicationStub->method('getTerm')->will($this->returnValue(24));
        $loanApplicationStub->method('getAmount')->will($this->returnValue(500.00));

        $fee = $feeCalculator->calculate($loanApplicationStub);
        $this->assertInternalType('float', $fee);
        $this->assertEquals(70.00, $fee);
    }

    /**
     * If the application loan amount is higher than set maximum, set the application loan amount to match and proceed. 
     */
    public function testApplicationAmountHigherThanSetMaximum() {
        $feeRepositoryStub = $this->createMock(FeeRepository::class);
        $feeRepositoryStub->method('find')->will(
                $this->onConsecutiveCalls(new Fee(760.00, 19000.00), new Fee(800.00, 20000.00))
        );
        $feeCalculator = new FeeCalculator($feeRepositoryStub);

        $loanApplicationStub = $this->createMock(LoanApplication::class);
        $loanApplicationStub->method('getTerm')->will($this->returnValue(24));
        $loanApplicationStub->method('getAmount')->will($this->returnValue(20001.73));

        $fee = $feeCalculator->calculate($loanApplicationStub);
        $this->assertInternalType('float', $fee);
        $this->assertEquals(800.00, $fee);
    }

    /**
     * If only an upper range is found, and no lower range then set the returned fee to be that of the found upper range.
     */
    public function testUpperRangeWithNoLowerRangeFound() {
        $feeRepositoryStub = $this->createMock(FeeRepository::class);
        $feeRepositoryStub->expects($this->at(0))->method('find')->will($this->returnValue(null));
        $feeRepositoryStub->expects($this->at(1))->method('find')->will($this->returnValue(new Fee(120.00, 3000.00)));
        $feeCalculator = new FeeCalculator($feeRepositoryStub);

        $loanApplicationStub = $this->createMock(LoanApplication::class);
        $loanApplicationStub->method('getTerm')->will($this->returnValue(24));
        $loanApplicationStub->method('getAmount')->will($this->returnValue(2650.00));

        $fee = $feeCalculator->calculate($loanApplicationStub);
        $this->assertInternalType('float', $fee);
        $this->assertEquals(120.00, $fee);
    }

    /**
     * Interface definition defines always a float return.
     * Maybe should return null instead of 0.00 if no upper fee found?
     * If no lower range is found, then return 0.00 to signify unable to calculate. 
     */
    public function testLowerRangeWithNoUpperRangeFound() {
        $feeRepositoryStub = $this->createMock(FeeRepository::class);
        $feeRepositoryStub->expects($this->at(0))->method('find')->will($this->returnValue(new Fee(120.00, 3000.00)));
        $feeRepositoryStub->expects($this->at(1))->method('find')->will($this->returnValue(null));
        $feeCalculator = new FeeCalculator($feeRepositoryStub);

        $loanApplicationStub = $this->createMock(LoanApplication::class);
        $loanApplicationStub->method('getTerm')->will($this->returnValue(24));
        $loanApplicationStub->method('getAmount')->will($this->returnValue(2900.00));

        $fee = $feeCalculator->calculate($loanApplicationStub);
        $this->assertInternalType('float', $fee);
        $this->assertEquals(0.00, $fee);
    }

    /**
     * If division by zero error exception occurs then return the highest fee.
     */
    public function testDivisionByZeroExceptionWithReturnOfMaxFee() {
        $feeRepositoryStub = $this->createMock(FeeRepository::class);
        $feeRepositoryStub->expects($this->at(0))->method('find')->will($this->returnValue(new Fee(120.00, 3000.00)));
        $feeRepositoryStub->expects($this->at(1))->method('find')->will($this->returnValue(new Fee(120.00, 3000.00)));
        $feeCalculator = new FeeCalculator($feeRepositoryStub);

        $loanApplicationStub = $this->createMock(LoanApplication::class);
        $loanApplicationStub->method('getTerm')->will($this->returnValue(24));
        $loanApplicationStub->method('getAmount')->will($this->returnValue(3000.00));

        $fee = $feeCalculator->calculate($loanApplicationStub);
        $this->assertInternalType('float', $fee);
        $this->assertEquals(120.00, $fee);
    }

}
