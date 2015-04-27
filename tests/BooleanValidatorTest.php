<?php

use valify\Validator;

class BooleanValidatorTest extends \PHPUnit_Framework_TestCase {
    private $validator;

    function setUp() {
        $this->validator = new Validator();
    }

    public function testStringsOneAndZeroAreBoolTrue()
    {
        $validator = $this->validator->setRules([[['bool1', 'bool2'], 'boolean']]);

        $isValid = $validator->loadData(['bool1'=>'1', 'bool2'=>'0'])->validate();
        $this->assertEquals(true, $isValid);
    }

    public function testIntOneAndZeroAreBoolTrue()
    {
        $validator = $this->validator->setRules([[['bool1', 'bool2'], 'boolean']]);

        $isValid = $validator->loadData(['bool1'=>1, 'bool2'=>0])->validate();
        $this->assertEquals(true, $isValid);
    }

    public function testStrictStringOneAndZeroAreBoolTrue()
    {
        $validator = $this->validator->setRules([[['bool1', 'bool2'], 'boolean', 'strict'=>true]]);

        $isValid = $validator->loadData(['bool1'=>'1', 'bool2'=>'0'])->validate();
        $this->assertEquals(true, $isValid);
    }

    public function testStrictIntOneAndZeroAreNotValid()
    {
        $validator = $this->validator->setRules([[['bool1', 'bool2'], 'boolean', 'strict'=>true]]);

        $isValid = $validator->loadData(['bool1'=>1, 'bool2'=>0])->validate();
        $this->assertEquals(false, $isValid);
    }

    public function testAnythingElseIsNotValid()
    {
        $data = [
            'notBool1'=>'bar',
            'notBool2'=>2,
            'notBool3'=>123.456,
            'notBool4'=>true,
            'notBool7'=>null
        ];

        $validator = $this->validator->setRules([[array_keys($data), 'boolean']]);

        $isValid = $validator->loadData($data)->validate();
        $this->assertEquals(false, $isValid);
    }
}
