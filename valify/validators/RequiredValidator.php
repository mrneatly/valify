<?php

namespace valify\validators;

class RequiredValidator extends AbstractValidator {
    public $allowEmpty = false;
    public $message = 'Value is empty';
    public $notEqual = '{value} should be equal to {requiredValue}';

    /**
     * @var boolean whether to skip this validator if the value being validated is empty.
     */
//    public $skipOnEmpty = false;

    /**
     * @var mixed the desired value that the attribute must have.
     * If this is null, the validator will validate that the specified attribute is not empty.
     * If this is set as a value that is not null, the validator will validate that
     * the attribute has a value that is the same as this property value.
     * Defaults to null.
     * @see strict
     */
    public $requiredValue;

    /**
     * @var boolean whether the comparison between the attribute value and [[requiredValue]] is strict.
     * When this is true, both the values and types must match.
     * Defaults to false, meaning only the values need to match.
     * Note that when [[requiredValue]] is null, if this property is true, the validator will check
     * if the attribute value is null; If this property is false, the validator will call [[isEmpty]]
     * to check if the attribute value is empty.
     */
    public $strict = false;

    protected function validateValue($value) {
        if ($this->requiredValue === null) {
            $value = is_string($value) ? trim($value) : $value;
            if ($this->strict && $value === null || !$this->strict && empty($value)) {
                $this->addError($this->message);
            }
        } elseif (!$this->strict && $value != $this->requiredValue || $this->strict && $value !== $this->requiredValue) {
            $this->addError($this->message, ['{requiredValue}' => $this->requiredValue]);
        }
    }
}