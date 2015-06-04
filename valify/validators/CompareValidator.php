<?php

namespace valify\validators;

class CompareValidator extends AbstractValidator {
    /**
     * @var string the name of the attribute to be compared with. When both this property
     * and [[compareValue]] are set, the latter takes precedence. If neither is set,
     * it assumes the comparison is against another attribute whose name is formed by
     * appending '_repeat' to the attribute being validated. For example, if 'password' is
     * being validated, then the attribute to be compared would be 'password_repeat'.
     * @see compareValue
     */
    public $compareAttribute;

    /**
     * @var mixed the constant value to be compared with. When both this property
     * and [[compareAttribute]] are set, this property takes precedence.
     * @see compareAttribute
     */
    public $compareValue;

    /**
     * @var string the type of the values being compared. The follow types are supported:
     *
     * - string: the values are being compared as strings. No conversion will be done before comparison.
     * - number: the values are being compared as numbers. String values will be converted into numbers before comparison.
     */
    public $type = 'string';

    /**
     * @var string the operator for comparison. The following operators are supported:
     *
     * - `==`: check if two values are equal. The comparison is done is non-strict mode.
     * - `===`: check if two values are equal. The comparison is done is strict mode.
     * - `!=`: check if two values are NOT equal. The comparison is done is non-strict mode.
     * - `!==`: check if two values are NOT equal. The comparison is done is strict mode.
     * - `>`: check if value being validated is greater than the value being compared with.
     * - `>=`: check if value being validated is greater than or equal to the value being compared with.
     * - `<`: check if value being validated is less than the value being compared with.
     * - `<=`: check if value being validated is less than or equal to the value being compared with.
     */
    public $operator = '==';

    public function init()
    {
        if ($this->message === null) {
            switch ($this->operator) {
                case '==':
                    $this->message = '{compareAttribute} must be repeated exactly.';
                    break;
                case '===':
                    $this->message = '{compareAttribute} must be repeated exactly.';
                    break;
                case '!=':
                    $this->message = '{compareAttribute} must not be equal to "{compareValue}".';
                    break;
                case '!==':
                    $this->message = '{compareAttribute} must not be equal to "{compareValue}".';
                    break;
                case '>':
                    $this->message = '{compareAttribute} must be greater than "{compareValue}".';
                    break;
                case '>=':
                    $this->message = '{compareAttribute} must be greater than or equal to "{compareValue}".';
                    break;
                case '<':
                    $this->message = '{compareAttribute} must be less than "{compareValue}".';
                    break;
                case '<=':
                    $this->message = '{compareAttribute} must be less than or equal to "{compareValue}".';
                    break;
                default:
                    $this->addError('Unknown operator: {operator}', ['{operator}' => $this->operator]);
            }
        }

        parent::init();
    }

    protected function validateValue($value) {
        if ( !$this->isComparable($value) ) {
            $this->addError('{attribute} is invalid.');
        } else {
            if ($this->compareValue !== null) {
                $compareAttribute = $compareValue = $this->compareValue;
            } else {
                $compareAttribute = $this->compareAttribute === null ? $this->attribute . '_repeat' : $this->compareAttribute;
                $compareValue = isset( $this->_data[$compareAttribute] ) ? $this->_data[$compareAttribute] : null;
            }

            if ( !$this->compareValues($this->operator, $value, $compareValue) ) {
                $this->addError($this->message, [
                    '{compareAttribute}' => $compareAttribute,
                    '{compareValue}' => $compareValue,
                ]);
            }
        }
    }

    private function compareValues($operator, $value, $compareValue)
    {
        switch ($operator) {
            case '==':
                return $value == $compareValue;
            case '===':
                return $value === $compareValue;
            case '!=':
                return $value != $compareValue;
            case '!==':
                return $value !== $compareValue;
            case '>':
                return $value > $compareValue;
            case '>=':
                return $value >= $compareValue;
            case '<':
                return $value < $compareValue;
            case '<=':
                return $value <= $compareValue;
            default:
                return false;
        }
    }

    private function isComparable($value) {
        return ( is_null($value) || is_bool($value) || is_numeric($value) || is_string($value) || is_array($value) || is_object($value) );
    }
}