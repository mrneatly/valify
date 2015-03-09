<?php

namespace valify\validators;

class BooleanValidator extends AbstractValidator {
    public $message = 'Value is not a boolean';
    /**
     * @var mixed the value representing true status. Defaults to '1'.
     */
    public $trueValue = '1';

    /**
     * @var mixed the value representing false status. Defaults to '0'.
     */
    public $falseValue = '0';

    /**
     * @var boolean whether the comparison to [[trueValue]] and [[falseValue]] is strict.
     * When this is true, the attribute value and type must both match those of [[trueValue]] or [[falseValue]].
     * Defaults to false, meaning only the value needs to be matched.
     */
    public $strict = false;

    protected function validateValue($value) {
        $valid = !$this->strict && ($value == $this->trueValue || $value == $this->falseValue)
            || $this->strict && ($value === $this->trueValue || $value === $this->falseValue);
        if (!$valid)
            $this->addError($this->message);
    }
}