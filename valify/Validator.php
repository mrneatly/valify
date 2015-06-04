<?php

namespace valify;

class Validator {
    private $_errors = [];
    private $_rules = [];
    private $_data = [];
    private $_currentValidator;
    private $_builtInValidators = [
        'boolean'  => '\valify\validators\BooleanValidator',
        'compare'  => '\valify\validators\CompareValidator',
        'date'     => '\valify\validators\DateValidator',
        'default'  => '\valify\validators\DefaultValueValidator',
        'email'    => '\valify\validators\EmailValidator',
        'file'     => '\valify\validators\FileValidator',
        'image'    => '\valify\validators\ImageValidator',
        'in'       => '\valify\validators\RangeValidator',
        'match'    => '\valify\validators\RegularExpressionValidator',
        'number'   => '\valify\validators\NumberValidator',
        'phone'    => '\valify\validators\PhoneValidator',
        'required' => '\valify\validators\RequiredValidator',
        'string'   => '\valify\validators\StringValidator',
        'unique'   => '\valify\validators\UniqueValidator',
        'url'      => '\valify\validators\UrlValidator',
    ];

    /**
     * You can perform a single validation by using this method.
     * Result of validate() method (boolean) will be returned.
     *
     * @param $name string - Name of validator
     * @param $value mixed - Value to validate. If array,
     * all keys are taken as attributes and values as values.
     * @param $params array - Params for a validator
     * @return object
     * @throws \Exception
     */
    public static function validateFor($name, $value, array $params = []) {
        if( !is_string($name) )
            throw new \UnexpectedValueException("Validator name must be a string, " . gettype($name) . " given");

        $rules = [];
        # By default, empty value should be not valid
        $params = array_merge(['allowEmpty'=>false], $params);

        if( is_array($value) ) {
            $rules[] = array_merge([array_keys($value), $name], $params);
        } else {
            $rules[] = array_merge([$name, $name], $params);
            $value = [$name => $value];
        }

        $v = new Validator();
        $result = new \stdClass();
        $result->isValid = $v->setRules($rules)->loadData($value)->validate();
        $result->lastError = $v->getLastError();
        $result->errors = $v->getErrors();
        unset($v);

        return $result;
    }

    /**
     * You can call this method multiple times. New rules
     * will be merged with already loaded ones.
     *
     * @param array $rules
     * @return $this
     */
    public function setRules(array $rules = []) {
        foreach ($rules as $rule) {
            if( !is_array($rule) || count($rule) < 2 )
                throw new \UnexpectedValueException("Every rule must be provided as an array and must include validator name and value attribute");

            if( !is_string($rule[1]) )
                throw new \UnexpectedValueException("Validator name must be a string, " . gettype($rule[1]) . " given");
        }

        $this->_rules = array_merge($this->_rules, $rules);
        return $this;
    }

    /**
     * You can call this method multiple times. New data
     * will be merged with already loaded one.
     *
     * @param array $data
     * @return $this
     */
    public function loadData(array $data = []) {
        $this->_data = array_merge($this->_data, $data);
        return $this;
    }

    /**
     * @return bool
     */
    public function validate() {
        # Sort rules by validator name
        usort($this->_rules, function ($a, $b) {
            if ($a[1] == $b[1]) return 0;
            return ($a[1] < $b[1]) ? -1 : 1;
        });

        foreach ($this->_rules as $rule) {
            $attribute = array_shift($rule);
            $validatorName = array_shift($rule);

            if($validatorName) {
                if( is_string($attribute) ) {
                    $value = isset($this->_data[$attribute]) ? $this->_data[$attribute] : null;
                    $this->callValidator($validatorName, [$attribute => $value], $rule);
                } elseif( is_array($attribute) ) {
                    $safeData = array_intersect_key( $this->_data, array_flip($attribute) );
                    $this->callValidator($validatorName, $safeData, $rule);
                }
            }
        }

        return !$this->hasErrors();
    }

    /**
     * After using validate(), we can
     * simply check, if there are any errors
     *
     * @return bool
     */
    public function hasErrors() {
        return !empty($this->_errors);
    }

    /**
     * Get all error messages, or the only ones
     * for a particular attribute, if it is defined
     *
     * @return array
     */
    public function getErrors($attribute = null) {
        return isset($this->_errors[$attribute]) ? $this->_errors[$attribute] : $this->_errors;
    }

    /**
     * Get a single error message for a particular attribute
     *
     * @param $attribute
     * @return array|null
     */
    public function getError($attribute) {
        return isset($this->_errors[$attribute]) ? $this->_errors[$attribute][0] : null;
    }

    /**
     * @return null
     */
    public function getLastError() {
        $errors = array_values($this->_errors);
        if( isset($errors[0][0]) )
            return $errors[0][0];
        return null;
    }

    private function callValidator($validator, $data, $rule = []) {
        if( isset($this->_builtInValidators[$validator]) ) {
            $namespace = $this->_builtInValidators[$validator];
            $validator = $this->loadValidator($namespace);
        } elseif( strpos($validator, '\\') !== false ) { # Validator name is a namespace
            $validator = $this->loadValidator($validator);

            if( !is_subclass_of($validator, '\valify\validators\AbstractValidator', false) )
                throw new \DomainException("Validator " . get_class($validator) . " must extend \\valify\\validators\\AbstractValidator class");
        }

        if( is_object($validator) ) {
            /** @var $validator validators\AbstractValidator */
            $validator = $this->setValidatorProperties($validator, $rule);

            foreach ($data as $attr => $val) {
                $validator->setAttributeAndValue($attr, $val);
                $validator->init();

                if( $validator->gotErrors() )
                    $this->setErrorStack($validator->fetchErrors());
            }
        } else {
            throw new \UnexpectedValueException("Validator " . get_class($validator) . " not found");
        }
    }

    private function loadValidator($name) {
        $currentValidatorName = trim(get_class($this->_currentValidator), '\\');
        $name = trim($name, '\\');

        if(!$this->_currentValidator || $currentValidatorName !== $name) {
            $validator = new $name();
            $this->_currentValidator = $validator;
        } else {
            $validator = $this->_currentValidator;
        }

        return $validator;
    }

    private function setValidatorProperties($obj, $params) {
        foreach ($params as $prop => $value)
            $obj->$prop = $value;

        $obj->_data = $this->_data;

        return $obj;
    }

    private function setErrorStack($errors) {
        foreach ($errors as $attr => $msgs)
            $this->_errors[$attr] = $msgs;
    }
}