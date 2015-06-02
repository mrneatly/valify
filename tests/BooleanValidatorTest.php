<?php

namespace tests;

use valify\Validator;

class BooleanValidatorTest extends \PHPUnit_Framework_TestCase {

    public function testStringIsValid()
    {
        $isValid = Validator::validateFor('boolean', ['bool1'=>'1', 'bool2'=>'0'])->isValid;

        $this->assertTrue($isValid);
    }

    public function testIntIsValid()
    {
        $isValid = Validator::validateFor('boolean', ['bool1'=>1, 'bool2'=>0])->isValid;

        $this->assertTrue($isValid);
    }

    public function testStrictStringIsValid()
    {
        $isValid = Validator::validateFor('boolean', ['bool1'=>'1', 'bool2'=>'0'], ['strict'=>true])->isValid;

        $this->assertTrue($isValid);
    }

    public function testStrictIntIsValid()
    {
        $isValid = Validator::validateFor('boolean', ['bool1'=>1, 'bool2'=>0], ['strict'=>true])->isValid;

        $this->assertFalse($isValid);
    }

    public function testStrictAnythingElseIsValid()
    {
        $data = [
            'notBool1'=>'bar',
            'notBool2'=>2,
            'notBool3'=>123.456,
            'notBool4'=>true,
            'notBool5'=>null
        ];

        $isValid = Validator::validateFor('boolean', $data, ['strict'=>true])->isValid;

        $this->assertFalse($isValid);
    }
}
