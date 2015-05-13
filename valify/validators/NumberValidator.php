<?php

namespace valify\validators;

class NumberValidator extends AbstractValidator {
    /**
     * @var boolean whether the attribute value can only be an integer. Defaults to false.
     */
    public $integerOnly = false;

    /**
     * @var integer|float upper limit of the number. Defaults to null, meaning no upper limit.
     * @see tooBig for the customized message used when the number is too big.
     */
    public $max;

    /**
     * @var integer|float lower limit of the number. Defaults to null, meaning no lower limit.
     * @see tooSmall for the customized message used when the number is too small.
     */
    public $min;

    /**
     * @var string user-defined error message used when the value is bigger than [[max]].
     */
    public $tooBig = '{attribute} must be no greater than {max}.';

    /**
     * @var string user-defined error message used when the value is smaller than [[min]].
     */
    public $tooSmall = '{attribute} must be no less than {min}.';

    /**
     * @var string the regular expression for matching integers.
     */
    public $integerPattern = '/^\s*[+-]?\d+\s*$/';

    /**
     * @var string the regular expression for matching numbers. It defaults to a pattern
     * that matches floating numbers with optional exponential part (e.g. -1.23e-10).
     */
//    public $numberPattern = '/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/';

    public $message = '{attribute} is not a valid number.';

    protected function validateValue($value) {
        if ( !is_numeric($value) ) {
            $this->addError($this->message);
        } elseif($this->integerOnly && !preg_match($this->integerPattern, "$value") ) {
            $this->addError($this->message);
        } elseif ($this->min !== null && $value < $this->min) {
            $this->addError($this->tooSmall, ['min' => $this->min]);
        } elseif ($this->max !== null && $value > $this->max) {
            $this->addError($this->tooBig, ['max' => $this->max]);
        }
    }
}