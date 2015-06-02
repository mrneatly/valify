<?php

namespace tests;

use valify\Validator;

class EmailValidatorTest extends \PHPUnit_Framework_TestCase {

    public function testIsInvalidInputTypeValid()
    {
        $data = [
            123456789,
            123.456,
            true,
            null
        ];

        $isValid = Validator::validateFor('email', $data)->isValid;

        $this->assertFalse($isValid);
    }

    public function testIsRegExpWorkingProperly() {
        $data = [
            'gmail.com',
            'address@gmail',
        ];

        $isValid = Validator::validateFor('email', $data)->isValid;

        $this->assertFalse($isValid);
    }

    public function testIsDnsFake() {
        $isValid = Validator::validateFor('email', 'address@fakedns.eu', ['checkDNS'=>true])->isValid;

        $this->assertFalse($isValid);
    }

    public function testIsEmailValid() {
        $isValid = Validator::validateFor('email', 'address@gmail.com', ['checkDNS'=>true])->isValid;

        $this->assertTrue($isValid);
    }
}
