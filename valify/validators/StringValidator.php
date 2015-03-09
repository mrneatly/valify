<?php

namespace valify\validators;

class StringValidator extends AbstractValidator {
    public $message = 'Value is not a valid string';
    /**
     * @var integer|array specifies the length limit of the value to be validated.
     * This can be specified in one of the following forms:
     *
     * - an integer: the exact length that the value should be of;
     * - an array of one element: the minimum length that the value should be of. For example, `[8]`.
     * This will overwrite [[min]].
     * - an array of two elements: the minimum and maximum lengths that the value should be of.
     * For example, `[8, 128]`. This will overwrite both [[min]] and [[max]].
     * @see tooShort for the customized message for a too short string.
     * @see tooLong for the customized message for a too long string.
     * @see notEqual for the customized message for a string that does not match desired length.
     */
    public $length;

    /**
     * @var integer maximum length. If not set, it means no maximum length limit.
     * @see tooLong for the customized message for a too long string.
     */
    public $max;

    /**
     * @var integer minimum length. If not set, it means no minimum length limit.
     * @see tooShort for the customized message for a too short string.
     */
    public $min;

    /**
     * @var string user-defined error message used when the length of the value is not equal to [[length]].
     */
    public $notEqual = '{value} is not equal to {length}';

    /**
     * @var string user-defined error message used when the length of the value is greater than [[max]].
     */
    public $tooLong = '{attribute} should not exceed {max} chars';

    /**
     * @var string user-defined error message used when the length of the value is smaller than [[min]].
     */
    public $tooShort = '{attribute} should not be shorter than {min} chars';

    /**
     * $encoding = 'UTF-8';
     */
    public $encoding = 'UTF-8';


    protected function validateValue($value) {
        if (!is_string($value))
            $this->addError($this->message);

        $length = mb_strlen($value, $this->encoding);

        if ($this->min !== null && $length < $this->min)
            $this->addError($this->tooShort, ['{min}' => $this->min]);

        if ($this->max !== null && $length > $this->max)
            $this->addError($this->tooLong, ['{max}' => $this->max]);

        if ($this->length !== null && $length !== $this->length)
            $this->addError($this->notEqual, ['{length}' => $this->length]);
    }
}