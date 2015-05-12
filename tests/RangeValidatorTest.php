<?php

namespace tests;

use valify\Validator;

class RangeValidatorTest extends \PHPUnit_Framework_TestCase {

    /**
     * @expectedException     \InvalidArgumentException
     */
    public function testIsExceptionThrownWithoutRangeProperty() {
        Validator::validateFor('in', '');
    }

    public function testIsOutOfRangeValid() {
        $isValid = Validator::validateFor('in', 6, ['range'=>range(1,5)]);
        $this->assertFalse($isValid);
    }

    public function testIsOutOfRangeValidWithInvertedComparison() {
        $isValid = Validator::validateFor('in', 6, ['range'=>range(1,5), 'not'=>true]);
        $this->assertTrue($isValid);
    }

    public function testIsSoftComparisonWorking() {
        $isValid = Validator::validateFor('in', "5", ['range'=>range(1,5)]);
        $this->assertTrue($isValid);
    }

    public function testIsStrictComparisonWorking() {
        $isValid = Validator::validateFor('in', "5", ['range'=>range(1,5), 'strict'=>true]);
        $this->assertFalse($isValid);
    }
}
