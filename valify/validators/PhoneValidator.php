<?php

namespace valify\validators;

class PhoneValidator extends AbstractValidator {
    public $message = "Phone number is not valid";

    /**
     * @var bool whether to check country code, preceding the phone number. Defaults to false.
     */
    public $checkCountryCode = true;

    /**
     * @var string the regular expression used to validate the attribute value
     * Example: +999 55555555
     */
    public $pattern = '/^[+]([\d]{1,3})[\(\.\-\s](([\d]{1,3})[\)\.\-\s]*)?(([\d]{3,5})[\.\-\s]?([\d]{4})|([\d]{2}[\.\-\s]?){4})$/';

    public $phonePattern = '/^(([\d]{3,5})[\.\-\s]?([\d]{4})|([\d]{2}[\.\-\s]?){4})$/';

    protected function validateValue($value) {
        if (!is_string($value) || strlen($value) >= 20) {
            $this->addError($this->message);
        } else {
            $pattern = $this->checkCountryCode ? $this->pattern : $this->phonePattern;
            $valid = preg_match($pattern, trim($value));
            if (!$valid)
                $this->addError($this->message);
        }
    }
}