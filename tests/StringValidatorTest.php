<?php

use valify\Validator;

class StringValidatorTest extends \PHPUnit_Framework_TestCase {
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

        $validator = $this->validator->setRules([[array_keys($data), 'string']]);

        $isValid = $validator->loadData($data)->validate();
        $this->assertEquals(false, $isValid);
    }

    public function testStringLengthIsNotEqual()
    {
        $data = ['123qwe456rty'];

        $validator = $this->validator->setRules([[array_keys($data), 'string', 'length'=>6]]);

        $isValid = $validator->loadData($data)->validate();
        $this->assertEquals(false, $isValid);
    }

    public function testStringLengthIsTooLong()
    {
        $data = ['123qwe456rty'];

        $validator = $this->validator->setRules([[array_keys($data), 'string', 'max'=>6]]);

        $isValid = $validator->loadData($data)->validate();
        $this->assertEquals(false, $isValid);
    }

    public function testStringLengthIsTooShort()
    {
        $data = ['123qwe456rty'];

        $validator = $this->validator->setRules([[array_keys($data), 'string', 'min'=>60]]);

        $isValid = $validator->loadData($data)->validate();
        $this->assertEquals(false, $isValid);
    }

    public function testStringIsValid()
    {
        $data = ['123qwe456rty'];

        $validator = $this->validator->setRules([[array_keys($data), 'string', 'min'=>6, 'max'=>20]]);

        $isValid = $validator->loadData($data)->validate();
        $this->assertEquals(true, $isValid);
    }
}
