<?php

namespace example;

use \valify\validators\AbstractValidator;

/**
 * An example class, constructed to give an image of how to make own validator.
 * By using own validator, in rules, define validator name as a namespace:
 $rules = [
    ['email', '\\example\\ExampleValidator']
 ];
 *
 * Class ExampleValidator
 * @package example
 */
class ExampleValidator extends AbstractValidator {

    /**
     * You can define as much properties as you need.
     * They are automatically set with values from
     * corresponding keys from rule.
     *
     * @var $ownProperty
     */
    public $ownProperty;

    /**
     * You may override parent constructor and
     * do here something before validator init() execution.
     * For example, you can redefine attribute or its value.
     * It is completely safe to remove this method from here -
     * parent constructor will be executed anyway.
     * NB! Defining constructor params and calling
     * parent constructor before your logic is required.
     *
     * @param $attribute
     * @param $value
     */
    function __construct($attribute, $value) {
        parent::__construct($attribute, $value);
        // Your code here
    }

    /**
     * You can override this method to do some job
     * right after validator constructor.
     * For example, you can define extra object properties,
     * or modify predefined ones.
     * It is completely safe to remove this method from here -
     * parent init() method will be executed anyway.
     * NB! Parent init() method call after your logic is required.
     */
    public function init() {
        // Your code here
        parent::init();
    }

    /**
     * This method is required.
     * Do here your validation magic. You have an
     * access to the value you are going to validate.
     * Attribute name is omitted here, but if you
     * definitely need it, ask it from $this->attribute.
     *
     * @param $value
     */
    protected function validateValue($value) {
        // Set an error message with some params:
        $this->addError('Example error; Called at: {time}', ['{time}'=>date('H:i')]);

        // Your validation code here
    }
}