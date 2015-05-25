<?php

namespace tests;

use valify\Validator;

class RequiredValidatorTest extends \PHPUnit_Framework_TestCase {
    public function testIsEmptyValid()
    {
        $isValid = Validator::validateFor('required', '')->isValid;

        $this->assertFalse($isValid);
    }

    public function testIsEmptyValidUsingStrictComparison()
    {
        $isValid = Validator::validateFor('required', '', ['strict'=>true])->isValid;

        $this->assertTrue($isValid);
    }

    public function testAreSpacesValid()
    {
        $isValid = Validator::validateFor('required', '    ')->isValid;

        $this->assertFalse($isValid);
    }

    public function testIsEqualToRequiredValue()
    {
        $isValid = Validator::validateFor('required', '1', ['requiredValue'=>1])->isValid;

        $this->assertTrue($isValid);
    }

    public function testIsEqualToRequiredValueUsingStrictComparison()
    {
        $isValid = Validator::validateFor('required', '1', ['requiredValue'=>1, 'strict'=>true])->isValid;

        $this->assertFalse($isValid);
    }
}
