# Valify
A little framework for user input validation. It is still in development, so keep an eye on commits. 
Inspired by [Yii2 input validation implementation](https://github.com/yiisoft/yii2/blob/master/docs/guide/input-validation.md).

## Requirements
You need PHP 5.4 to run this code.

## Installation
After downloading source, add next code to file, where you data is going to be validated:
 
 ```php
 require 'valify/Validator.php';
 $validator = new Validator();
 ```
 
Framework uses namespaces, so add next line to the top of file, where validator is called:

```php
use valify\Validator;
```

There is also a more straightforward way to install this framework through the compser.
In your project root, issue next command in terminal:

`php composer.phar require xphoenyx/valify 1.5.2`

Now you are ready to validate your data.

### Hint for a MVC pattern users

You can implement your own methods in base model class.
Please investigate an example below:

```php

use valify\Validator;

class Model {
    /* ... */
    
    protected $validator;
    // Your rules
    public $rules = [];
    // Here is stored $_POST, for example
    public $data = [];

    function __construct() {
        /* ... */
        $this->validator = new Validator();
        /* ... */
    }

    /*
     * Your other methods
     */

    public function validate() {
        return $this->validator
            ->setRules($this->rules)
            ->loadData($this->data)
            ->validate();
    }

    public function getErrors() {
        return $this->validator->getErrors();
    }
}
```

## Usage
Usage is similar to [Yii2 input validation](https://github.com/yiisoft/yii2/blob/master/docs/guide/input-validation.md).

### Define rules

```php
$rules = [
    [['username', 'password'], 'string', 'max'=>10],
    ['email', 'email', 'message'=>'Please provide a valid email'],
    ['remember_me', 'boolean']
];
```

Each validator accepts `message` parameter, which should contain an error message as string.
You can access attribute name and its value in `message` by using so-called 'patterns':

```php
['email', 'email', 'message'=>'{value} for attribute "{attribute}" is not a valid email'],
```

*NB! In case you want to show value in error message, you must check first if it can be represented as a string.* 

You can also implement your own validators by extending `valify\validator\AbstractValidator` class. 
In this case, if you are not using composer autoloader, you should also import (require) AbstractValidator.
To use own validator in rules, just define validator namespace as a validator name:

```php
$rules = [
    /* ... */
    ['email', '\\examples\\ExampleValidator', 'ownProperty'=>'abc' /* ... */]
    /* ... */
];
```

Do not forget to import your validator before defining a namespace in rules.
Refer to the `valify\validators\ExampleValidator` for detailed implementation info.

### Define data to be validated

Input data is expected in next format:

```php
$data = [
    'username'=>'username',
    'password'=>'123qwe',
    'email'=>'address@gmail.com',
    'remember_me'=>'1',
];
```

### Set rules and data

```php
$validator = new Validator();
$validator = $validator->setRules($rules)->loadData($data)
```

You can call `setrules()` and `loadData()` multiple times:

```php
$validator = new Validator();
$validator = $validator
                ->setRules([...])
                ->loadData([...])
                ->setRules([...])
                ->setRules([...])
                ->loadData([...])
                ->loadData([...]);
```

### Execute validation

```php
$isValid = $validator->validate();
```

You have an ability to perform a single value validation, without calling `setRules()` and `loadData()`:

```php
$password = $_POST['password'];
$isValid = Validator::validateFor('string', $password, ['min'=>6, 'max'=>20]);
```

In this case, `validateFor()` will return result of `validate()` method.

### Fetch error messages
 
```php
if($validator->hasErrors()) {
    $errorMsgs = $validator->getErrors();
}
```

You can also get an error message of a single attribute:

```php
$errorMsgForUserAttr = $validator->getError('username');
```

As each attribute can have a few error messages, `getError()` will give you 
the last message of the corresponding attribute error stack (array).

## List of built-in validators:
* boolean
* email
* file
* required
* string
* url

For detailed parameter description of each validator, see class methods in valify/validators.

## Testing
In order to properly run unit tests, you need to specify path to the composer autoloader file.
Then you just issue the `phpunit` command in terminal under `valify` (component root) directory.

## Examples
Check index.php in `examples` directory to view framework in action.

All bug and issue reports are welcome as well as improvement proposals. Enjoy.