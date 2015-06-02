<?php

namespace valify\validators;

class EmailValidator extends AbstractValidator {
    public $message = 'Email address is not valid';
    /**
     * @var string the regular expression used to validate the attribute value.
     * @see http://www.regular-expressions.info/email.html
     */
    private $pattern = '/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/';
    /**
     * @var boolean whether to check whether the email's domain exists and has either an A or MX record.
     * Be aware that this check can fail due to temporary DNS problems even if the email address is
     * valid and an email would be deliverable. Defaults to false.
     */
    public $checkDNS = false;
    public $DNSIsNotValid = '{domain} does not exist or does not have A or MX record';

    protected function validateValue($value) {
//        echo "<pre>";print_r($value);echo "</pre>";
        if (!is_string($value) || strlen($value) >= 320) {
            $this->addError($this->message);
        } elseif ( !preg_match('/^(.*<?)(.*)@(.*?)(>?)$/', $value, $matches) ) {
            $this->addError($this->message);
        } else {
            $domain = $matches[3];
            $valid = preg_match($this->pattern, $value);
            if (!$valid)
                $this->addError($this->message);
            if ($valid && $this->checkDNS) {
                $valid = checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A');
                if(!$valid)
                    $this->addError($this->DNSIsNotValid, ['{domain}'=>$domain]);
            }
        }
    }
}