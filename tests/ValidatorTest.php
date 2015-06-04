<?php

namespace tests;

use valify\Validator;

class ValidatorTest extends \PHPUnit_Framework_TestCase {
    public function testSingleValidationOnSingleValue() {
        $isValid = Validator::validateFor('string', '55555555', ['min'=>5, 'max'=>100])->isValid;

        $this->assertTrue($isValid);
    }

    public function testSingleValidationOnMultipleValues() {
        $emails = [
            'test@gmail.com',
            'test2@yahoo.com'
        ];

        $isValid = Validator::validateFor('email', $emails, ['checkDNS'=>true]);

        $this->assertTrue($isValid->isValid, $isValid->lastError);
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Validator name must be a string, array given
     */
    public function testIsValidatorNameAsNonStringValid() {
        $isValid = Validator::validateFor([], '');

        $this->assertFalse($isValid->isValid, $isValid->lastError);
    }

    /**
     * As no cycle run on empty array of values,
     * no any validation loaded,
     * so no any errors produced.
     */
    public function testIsEmptyArrayValidOnSingleValidation() {
        $isValid = Validator::validateFor('phone', []);

        $this->assertTrue($isValid->isValid, $isValid->lastError);
    }

    public function testIsEmptyValueValidOnSingleValidation() {
        $isValid = Validator::validateFor('phone', '');

        $this->assertFalse($isValid->isValid, $isValid->lastError);
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Every rule must be provided as an array and must include validator name and value attribute
     */
    public function testIsNonArrayRuleValid() {
        $v = new Validator();
        $rules = [
            new \stdClass(),
        ];

        $v->setRules($rules);
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Every rule must be provided as an array and must include validator name and value attribute
     */
    public function testIsRuleWithoutValidatorNameValid() {
        $v = new Validator();
        $rules = [[
            'first_name',
        ]];

        $v->setRules($rules);
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Validator name must be a string, object given
     */
    public function testIsNonStringValidatorNameValid() {
        $v = new Validator();
        $rules = [[
            'first_name',
            new \stdClass(),
        ]];

        $v->setRules($rules);
    }

    public function testLoadRulesAndDataMultipleTimes() {
        $v = new Validator();

        $v->loadData(['email' => 'test@gmail.com'])
            ->loadData(['first_name' => 'John']);

        $v->loadData(['last_name' => 'Doe'])
            ->setRules([['email', 'email']])
            ->setRules([[['first_name', 'last_name'], 'string', 'max'=>10]]);

        $isValid = $v->validate();

        $this->assertTrue($isValid);
    }
}
