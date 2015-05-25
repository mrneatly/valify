<?php

namespace tests;

use valify\Validator;

class NumberValidatorTest extends \PHPUnit_Framework_TestCase {
    public function testIsEmptyValid() {
        $isValid = Validator::validateFor('number', '')->isValid;

        $this->assertFalse($isValid);
    }

    public function testIsTextIsValid() {
        $isValid = Validator::validateFor('number', 'asfsa656')->isValid;

        $this->assertFalse($isValid);
    }

    public function testIsStringValid() {
        $isValid = Validator::validateFor('number', '656.35')->isValid;

        $this->assertTrue($isValid);
    }

    public function testIsValidWithIntegerOnlyEnabled() {
        $isValid = Validator::validateFor('number', 656.35, ['integerOnly'=>true])->isValid;

        $this->assertFalse($isValid);
    }

    public function testIsBiggerThanAllowedValid() {
        $isValid = Validator::validateFor('number', 656.35, ['max'=>656])->isValid;

        $this->assertFalse($isValid);
    }

    public function testIsLessThanAllowedValid() {
        $isValid = Validator::validateFor('number', 656.35, ['min'=>657])->isValid;

        $this->assertFalse($isValid);
    }
}
