<?php

namespace tests;

use valify\Validator;

class UrlValidatorTest extends \PHPUnit_Framework_TestCase {

    public function testIsEmptyValid()
    {
        $isValid = Validator::validateFor('url', '');

        $this->assertFalse($isValid);
    }

    public function testIsValidWithoutProtocol()
    {
        $isValid = Validator::validateFor('url', 'google.com');

        $this->assertFalse($isValid);
    }

    public function testIsValidWithoutTopLevelDomain()
    {
        $isValid = Validator::validateFor('url', 'http://google');

        $this->assertFalse($isValid);
    }

    public function testIsValidWithProtocol()
    {
        $isValid = Validator::validateFor('url', 'http://google.com');

        $this->assertTrue($isValid);
    }

    public function testIsProtocolValidationDisablingWorking()
    {
        $isValid = Validator::validateFor('url', 'http://google.com', ['validSchemes'=>['https']]);

        $this->assertFalse($isValid);
    }

    public function testIsDisabledIDNCheckWorking()
    {
        $isValid = Validator::validateFor('url', 'http://täst.de');

        $this->assertFalse($isValid);
    }

    public function testIsEnabledIDNCheckWorking()
    {
        $isValid = Validator::validateFor('url', 'http://täst.de', ['enableIDN'=>true]);

        $this->assertTrue($isValid);
    }
}
