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

        $isValid = Validator::validateFor('string', $data)->isValid;

        $this->assertFalse($isValid);
    }

    public function testIsStringWithLengthOutOfProperLengthValid()
    {
        $isValid = Validator::validateFor('string', '123qwe456rty', ['length'=>6])->isValid;

        $this->assertFalse($isValid);
    }

    public function testIsTooLongStringValid()
    {
        $isValid = Validator::validateFor('string', '123qwe456rty', ['max'=>6])->isValid;

        $this->assertFalse($isValid);
    }

    public function testIsTooShortStringValid()
    {
        $isValid = Validator::validateFor('string', '123qwe456rty', ['min'=>50])->isValid;

        $this->assertFalse($isValid);
    }

    public function testIsStringValidWithRestrictions()
    {
        $isValid = Validator::validateFor('string', '123qwe456rty', ['min'=>6, 'max'=>20])->isValid;

        $this->assertTrue($isValid);
    }
}
