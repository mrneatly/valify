<?php

use valify\Validator;

require '../valify/Validator.php';
$validator = new Validator();

//require 'examples/ExampleValidator.php';

$rules = [
    ['username', 'required'],
    [['username', 'password'], 'string', 'max'=>10],
    ['email', 'email', 'message'=>'Please provide a valid email'],
    ['remember_me', 'boolean'],
    ['file', 'file', 'minSize'=>10000, 'maxFiles'=>2, 'extensions'=>['jpg'], 'checkExtensionByMimeType'=>false]
//    ['email', '\\examples\\ExampleValidator']
];

if(!empty($_POST)) {
    $data = $_POST;
    $data['file'] = $_FILES['file'];
    $isValid = $validator->setRules($rules)->loadData($data)->validate();
}

function getValue($val) {
    return isset($_POST[$val]) ? $_POST[$val] : '';
}

?>

<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <style>
        .help-block {
            color: red;
        }
    </style>
</head>
<body>
    <div style="padding: 30px">
        <form class="form-horizontal" method="post" enctype="multipart/form-data">
            <fieldset>

                <!-- Form Name -->
                <legend>Test form</legend>

                <?php //echo "<pre>";print_r($validator->getErrors());echo "</pre>" ?>

                <?php if($validator->hasErrors()): ?>
                    <div class="alert alert-danger" role="alert">Input is not valid!</div>
                <?php endif ?>

                <!-- Text input-->
                <div class="control-group">
                    <label class="control-label" for="username">User name</label>
                    <div class="controls">
                        <input id="username" name="username" placeholder="user name" class="input-medium" type="text" value="<?=getValue('username')?>">
                        <p class="help-block"><?=$validator->getError('username')?></p>
                    </div>
                </div>

                <!-- Password input-->
                <div class="control-group">
                    <label class="control-label" for="password">Password</label>
                    <div class="controls">
                        <input id="password" name="password" placeholder="password" class="input-medium" type="password" value="<?=getValue('password')?>">
                        <p class="help-block"><?=$validator->getError('password')?></p>
                    </div>
                </div>

                <!-- Text input-->
                <div class="control-group">
                    <label class="control-label" for="email">Email address</label>
                    <div class="controls">
                        <input id="email" name="email" placeholder="email@domain.com" class="input-xlarge" type="text" value="<?=getValue('email')?>">
                        <p class="help-block"><?=$validator->getError('email')?></p>
                    </div>
                </div>

                <!-- Multiple Checkboxes (inline) -->
                <div class="control-group">
                    <label class="control-label" for="remember_me">Remember me</label>
                    <div class="controls">
                        <input name="remember_me" id="remember_me" type="checkbox">
                    </div>
                </div>

                <!-- File Button -->
                <div class="control-group">
                    <label class="control-label" for="file">Test file</label>
                    <div class="controls">
                        <input id="file" name="file[]" class="input-file" type="file">
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="file">Test file 2</label>
                    <div class="controls">
                        <input id="file" name="file[]" class="input-file" type="file">
                    </div>
                </div>
                <p class="help-block"><?=$validator->getError('file')?></p>

                <!-- Button -->
                <div class="control-group">
                    <div class="controls">
                        <input type="submit" id="singlebutton" name="singlebutton" class="btn btn-primary">
                    </div>
                </div>

            </fieldset>
        </form>
    </div>
</body>
</html>