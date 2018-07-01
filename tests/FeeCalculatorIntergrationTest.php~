<?php

use PHPUnit\Framework\TestCase;
use Lendable\Interview\Interpolation\Service\Fee\FeeCalculator;
use Lendable\Interview\Interpolation\Repository\FeeRepository;
use Lendable\Interview\Interpolation\Model\LoanApplication;

class FeeCalculatorIntergrationTest extends TestCase {

    /**
     * Example from readme.
     */
    public function testExampleTaskResponse() {
        $feeCalculator = new FeeCalculator(new FeeRepository(include('TestFeeList.php')));
        $fee = $feeCalculator->calculate(new LoanApplication(24, 2750.00));
        $this->assertInternalType('float', $fee);
        $this->assertEquals(115.00, $fee);
    }

}
