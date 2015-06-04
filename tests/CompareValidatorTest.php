<?php

namespace tests;

use valify\Validator;

class CompareValidatorTest extends \PHPUnit_Framework_TestCase {
    public function testIsInvalidCompareOperatorValid()
    {
        $isValid = Validator::validateFor('compare', 123, ['operator' => '='])->isValid;

        $this->assertFalse($isValid);
    }

    public function testAreRepresentableDataTypesValid()
    {
        $data = [
            null,
            1234,
            1234.5678
        ];

        $isValid = true;
        $message = null;
        foreach ($data as $value) {
            $valid = Validator::validateFor('compare', $value, ['compareValue' => $value]);
            if(!$valid->isValid) {
                $isValid = false;
                $message = $valid->error;
                break;
            }
        }

        $this->assertTrue($isValid, $message);
    }

    public function testSoftComparison() {
        $isValid = Validator::validateFor('compare', 1234, ['compareValue' => '1234'])->isValid;

        $this->assertTrue($isValid);
    }

    public function testStrictComparison() {
        $isValid = Validator::validateFor('compare', 12345, ['compareValue' => '12345', 'operator' => '==='])->isValid;

        $this->assertFalse($isValid);
    }

    public function testSoftInvertedComparison() {
        $isValid = Validator::validateFor('compare', 12345, ['compareValue' => '12345', 'operator' => '!='])->isValid;

        $this->assertFalse($isValid);
    }

    public function testStrictInvertedComparison() {
        $isValid = Validator::validateFor('compare', 12345, ['compareValue' => '12345', 'operator' => '!=='])->isValid;

        $this->assertTrue($isValid);
    }

    public function testBiggerThanComparison() {
        $isValid = Validator::validateFor('compare', 12345.2, ['compareValue' => 12345.1, 'operator' => '>'])->isValid;

        $this->assertTrue($isValid);
    }

    public function testMoreOrEqualComparison() {
        $data = [
            12344 => 12344,
            12345 => 12344
        ];

        $isValid = true;
        foreach ($data as $val => $compVal) {
            $valid = Validator::validateFor('compare', $val, ['compareValue' => $compVal, 'operator' => '>='])->isValid;
            if(!$valid)
                $isValid = false;
        }

        $this->assertTrue($isValid);
    }

    public function testLessThanComparison() {
        $isValid = Validator::validateFor('compare', 12345.1, ['compareValue' => 12345.2, 'operator' => '<'])->isValid;

        $this->assertTrue($isValid);
    }

    public function testLessOrEqualComparison() {
        $data = [
            12345 => 12345,
            12344 => 12345
        ];

        $isValid = true;
        foreach ($data as $val => $compVal) {
            $valid = Validator::validateFor('compare', $val, ['compareValue' => $compVal, 'operator' => '<='])->isValid;
            if(!$valid)
                $isValid = false;
        }

        $this->assertTrue($isValid);
    }
}
