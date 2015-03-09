<?php

namespace valify;

require_once "validators/AbstractValidator.php";

class Validator {
    private $_errors = [];
    private $_rules = [];
    private $_data = [];
    private $_builtInValidators = [
        'required' => '\valify\validators\RequiredValidator',
        'string'   => '\valify\validators\StringValidator',
        'email'    => '\valify\validators\EmailValidator',
        'boolean'  => '\valify\validators\BooleanValidator',
        'compare'  => '\valify\validators\CompareValidator',
        'date'     => '\valify\validators\DateValidator',
        'default'  => '\valify\validators\DefaultValueValidator',
        'double'   => '\valify\validators\NumberValidator',
        'exist'    => '\valify\validators\ExistValidator',
        'file'     => '\valify\validators\FileValidator',
        'image'    => '\valify\validators\ImageValidator',
        'in'       => '\valify\validators\RangeValidator',
        'integer'  => '\valify\validators\NumberValidator',
        'match'    => '\valify\validators\RegularExpressionValidator',
        'number'   => '\valify\validators\NumberValidator',
        'url'      => '\valify\validators\UrlValidator',
    ];

    /**
     * You can perform a single validation by using this method.
     * Result of validate() method (boolean) will be returned.
     *
     * @param $name string - Name of validator
     * @param $value mixed - Value to validate. If array,
     * all keys are taken as attributes and values as values.
     * @param array $params array - Params for a validator
     * @return bool
     * @throws \Exception
     */
    function validateFor($name, $value, $params = []) {
        $rules = [];

        if( is_array($value) ) {
            foreach ($value as $attr => $val)
                $rules[] = [$attr, $name, $params];
        } else {
            $rules[] = [$name, $name, $params];
            $value = [$name => $value];
        }

        return $this->setRules($rules)->loadData($value)->validate();
    }

    /**
     * You can call this method multiple times. New rules
     * will be merged with already loaded ones.
     *
     * @param array $rules
     * @return $this
     */
    public function setRules($rules = []) {
        if( !is_array($rules) )
            throw new \Exception("Rules must be provided as an array");

        foreach ($rules as $rule) {
            if( !is_array($rule) )
                throw new \Exception("Every rule must be provided as an array");
        }

        //TODO Rules could be set in JSON

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
    public function loadData($data = []) {
        if( !is_array($data) )
            throw new \Exception("Data must be provided as an array");

        //TODO Data could be set in JSON

        $this->_data = array_merge($this->_data, $data);
        return $this;
    }

    /**
     * @return bool
     */
    public function validate() {
        $data = $this->_data;
        $rules = $this->_rules;

        foreach ($data as $attr => $value) {
            foreach ($rules as $rule) {
                $ruleItems = array_values($rule);

                if( is_string($ruleItems[0]) && $ruleItems[0] == $attr ) {
                    $this->callValidator($value, $rule);
                } elseif( is_array($ruleItems[0]) && in_array($attr, $ruleItems[0]) ) {
                    // Replace first value from array to string
                    array_shift($rule);
                    array_unshift($rule, $attr);
                    $this->callValidator($value, $rule);
                }
            }
        }

        return $this->hasErrors();
    }

    /**
     * After using validate(), we can
     * just check, if there are any errors
     * @return bool
     */
    public function hasErrors() {
        return empty($this->_errors);
    }

    /**
     * @return array
     */
    public function getErrors() {
        // Prepare output here
        return $this->_errors;
    }

    /**
     * Get error of a particular attribute
     * @param $attribute
     * @return array|null
     */
    public function getError($attribute = null) {
        return isset($this->_errors[$attribute]) ? $this->_errors[$attribute] : null;
    }

    /**
     * @param array $rule
     */
    private function callValidator($value, $rule = []) {
        $attribute = array_shift($rule);
        $validatorName = array_shift($rule);

        if( isset($this->_builtInValidators[$validatorName]) ) {
            $namespace = $this->_builtInValidators[$validatorName];
            $className = substr($namespace, strrpos($namespace, '\\')+1);

            require_once 'validators/' . $className . '.php';
            $validator = new $namespace($attribute, $value);
        } elseif( strpos($validatorName, '\\') !== false ) { # Means that validator name is matched as a namespace
            $validator = new $validatorName($attribute, $value);

            if( !is_subclass_of($validator, '\valify\validators\AbstractValidator', false) )
                throw new \Exception("Validator $validatorName must extend \\valify\\validators\\AbstractValidator class");
        }

        if( isset($validator) ) {
            $validator = $this->setValidatorProperties($validator, $rule);

            $validator->init();
            if( $validator->gotErrors() )
                $this->setError($validator->fetchErrors());
        } else {
            throw new \Exception("Validator $validatorName not found");
        }
    }

    private function setValidatorProperties($obj, $params) {
        foreach ($params as $prop => $value)
            $obj->$prop = $value;

        return $obj;
    }

    private function setError($errors) {
        foreach ($errors as $attr => $msg)
            $this->_errors[$attr] = $msg;
    }
}