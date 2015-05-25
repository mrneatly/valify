<?php

namespace tests;

use valify\Validator;

class UrlValidatorTest extends \PHPUnit_Framework_TestCase {

    public function testIsEmptyValid()
    {
        $isValid = Validator::validateFor('url', '')->isValid;

        $this->assertFalse($isValid);
    }

    public function testIsValidWithoutProtocol()
    {
        $isValid = Validator::validateFor('url', 'google.com')->isValid;

        $this->assertFalse($isValid);
    }

    public function testIsValidWithoutTopLevelDomain()
    {
        $isValid = Validator::validateFor('url', 'http://google')->isValid;

        $this->assertFalse($isValid);
    }

    public function testIsValidWithProtocol()
    {
        $isValid = Validator::validateFor('url', 'http://google.com')->isValid;

        $this->assertTrue($isValid);
    }

    public function testIsProtocolValidationDisablingWorking()
    {
        $isValid = Validator::validateFor('url', 'http://google.com', ['validSchemes'=>['https']])->isValid;

        $this->assertFalse($isValid);
    }

    public function testIsDisabledIDNCheckWorking()
    {
        $isValid = Validator::validateFor('url', 'http://täst.de')->isValid;

        $this->assertFalse($isValid);
    }

    public function testIsEnabledIDNCheckWorking()
    {
        $isValid = Validator::validateFor('url', 'http://täst.de', ['enableIDN'=>true])->isValid;

        $this->assertTrue($isValid);
    }
}
