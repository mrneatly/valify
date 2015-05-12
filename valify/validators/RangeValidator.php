<?php

namespace valify\validators;

class RangeValidator extends AbstractValidator {
    /**
     * @var array list of valid values that the attribute value should be among
     */
    public $range;

    /**
     * @var boolean whether the comparison is strict (both type and value must be the same)
     */
    public $strict = false;

    /**
     * @var boolean whether to invert the validation logic. Defaults to false. If set to true,
     * the attribute value should NOT be among the list of values defined via [[range]].
     */
    public $not = false;

    public $message = '{attribute} is invalid';

    public $rangeErrorMessage = 'The "range" property must be set.';

    public function init() {
        if ( !is_array($this->range) )
            throw new \InvalidArgumentException($this->rangeErrorMessage);

        parent::init();
    }

    protected function validateValue($value) {
        $value = (is_array($value) ? $value : [$value]);

        $in = true;
        foreach ($value as $v) {
            if ( !in_array($v, $this->range, $this->strict) ) {
                $in = false;
                break;
            }
        }

        if($this->not === $in)
            $this->addError($this->message);
    }
}