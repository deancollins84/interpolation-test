<?php

use PHPUnit\Framework\TestCase;
use Lendable\Interview\Interpolation\Model\Fee;
use Lendable\Interview\Interpolation\Repository\FeeRepository;

class FeeRepositoryUnitTest extends TestCase {

    protected $feeRepository;

    /**
     * Inject test data source in to repository.
     */
    public function setup() {
        $dataSource = include('TestFeeList.php');
        $this->feeRepository = new FeeRepository($dataSource);
    }

    public function testUpperRangeFoundFor12MonthTerm() {
        $fee = $this->feeRepository->find(1000.00);
        $this->assertInstanceOf(Fee::class, $fee);
        $this->assertEquals(90.00, $fee->getAmount());
    }

    public function testUpperRangeNotFoundFor12MonthTerm() {
        $this->assertNull($this->feeRepository->find(30000.00));
    }

    public function testLowerRangeFoundFor12MonthTerm() {
        $fee = $this->feeRepository->find(2000.00, 12, 'GBP', false);
        $this->assertInstanceOf(Fee::class, $fee);
        $this->assertEquals(50.00, $fee->getAmount());
    }

    public function testLowerRangeNotFoundFor12MonthTerm() {
        $this->assertNull($this->feeRepository->find(1.00, 12, 'GBP', false));
    }

    public function testUpperRangeFoundFor24MonthTerm() {
        $fee = $this->feeRepository->find(1000.00, 24);
        $this->assertInstanceOf(Fee::class, $fee);
        $this->assertEquals(100.00, $fee->getAmount());
    }

    public function testUpperRangeNotFoundFor24MonthTerm() {
        $this->assertNull($this->feeRepository->find(30000.00, 24));
    }

    public function testLowerRangeFoundFor24MonthTerm() {
        $fee = $this->feeRepository->find(2000.00, 24, 'GBP', false);
        $this->assertInstanceOf(Fee::class, $fee);
        $this->assertEquals(70.00, $fee->getAmount());
    }

    public function testLowerRangeNotFoundFor24MonthTerm() {
      $this->assertNull($this->feeRepository->find(1.00, 24, 'GBP', false));
    }

    public function testUpperRangeFoundFor36MonthTerm() {
        $this->assertNull($this->feeRepository->find(1000.00, 36));
    }

    public function testUpperRangeNotFoundFor36MonthTerm() {
        $this->assertNull($this->feeRepository->find(3000.00, 36));
    }
    
    public function testCurrencyNotFound(){
        $this->assertNull($this->feeRepository->find(3000.00, 36, 'ZAR'));
    }
   
}
