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

Now you are ready to validate your data.

### In case you have a MVC-based app, here is a little hint for you:

You can implement your own methods in base model class.
Please investigate an example below:

```php
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

### Prepare data
- Define the rules for each incoming value

```php
$rules = [
    [['username', 'password'], 'string', 'max'=>10],
    ['email', 'email', 'message'=>'Please provide a valid email'],
    ['remember_me', 'boolean']
];
```

Each validator accepts a `message` parameter, which should contain an error message as string.
You can access attribute name and value in `message`, by using so-called 'patterns':

```php
['email', 'email', 'message'=>'{value} for {attribute} is not a valid email'],
```

*NB! In case you want to show value in error message, you must check if it can be represented as a string.* 

You cat also implement your own validators by extending `valify\validator\AbstractValidator` class. 
In this case you should import (require) AbstractValidator also.
To use own validator in rules, just define validator namespace in validator name:

```php
$rules = [
    /* ... */
    ['email', '\\example\\ExampleValidator', 'ownProperty'=>'abc' /* ... */]
    /* ... */
];
```

Do not forget to import your validator before defining s namespace in rules.
Refer to the `valify\validators\ExampleValidator` for detailed implementation info.

- Define the data to be validated

```php
$data = [
    'username'=>'username',
    'password'=>'123qwe',
    'email'=>'address@gmail.com',
    'remember_me'=>'1',
];
```

- Set rules and data

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

### Validate
- Execute validation

```php
$isValid = $validator->validate();
```

You can perform a single value validation (without calling `setRules()` and `loadData()`):

```php
$validator = new Validator();
$password = $_POST['password'];
$validator->validateFor('password', $password, ['min'=>6, 'max'=>20]);
```

In this case, `validateFor()` will return result of `validate()` method.

### Get results
- Get an array with error messages
 
```php
if($validator->hasErrors()) {
    $errorMsgs = $validator->getErrors();
}
```

You can also get an error message of a single attribute:

```php
$errorMsgForUserAttr = $validator->getError('username');
```

## List of built-in validators:
* boolean
* email
* string

For detailed parameter description of each validator, see class methods in valify/validators.

## Testing
In order to properly run unit tests, run them under `tests` directory.



All bug and issue reports are welcome as well as improvement proposals. Enjoy.