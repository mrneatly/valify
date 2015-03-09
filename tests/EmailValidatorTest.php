<?php

namespace tests;

use valify\Validator;

require_once '../valify/Validator.php';

class EmailValidatorTest extends \PHPUnit_Framework_TestCase {
    private $validator;

    function setUp() {
        $this->validator = new Validator();
    }

    public function testEverythingButTheStringIsNotValid()
    {
        $data = [
            123456789,
            123.456,
            true,
            null
        ];

        $validator = $this->validator->setRules([[array_keys($data), 'email']]);

        $isValid = $validator->loadData($data)->validate();
        $this->assertEquals(false, $isValid);
    }

    public function testIsRegExpWorkingProperly() {
        $data = [
            'gmail.com',
            'address@gmail',
        ];

        $validator = $this->validator->setRules([[array_keys($data), 'email']]);

        $isValid = $validator->loadData($data)->validate();
        $this->assertEquals(false, $isValid);
    }

    public function testIsDnsFake() {
        $data = [
            'address@fakedns.eu',
        ];

        $validator = $this->validator->setRules([[array_keys($data), 'email', 'checkDNS'=>true]]);

        $isValid = $validator->loadData($data)->validate();
        $this->assertEquals(false, $isValid);
    }

    public function testIsEmailValid() {
        $data = [
            'address@gmail.com',
        ];

        $validator = $this->validator->setRules([[array_keys($data), 'email', 'checkDNS'=>true]]);

        $isValid = $validator->loadData($data)->validate();
        $this->assertEquals(true, $isValid);
    }
}
