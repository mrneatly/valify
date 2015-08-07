<?php

namespace tests;

use valify\Validator;

class UniqueValidatorTest extends \PHPUnit_Framework_TestCase {

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Table and column must be specified
     */
    public function testIsExceptionThrownOnEmptyTableOrColumnParams() {
        Validator::validateFor('unique', '123', []);
    }

    /**
     * @expectedException \mysqli_sql_exception
     */
    public function testIsExceptionThrownOnEmptyConnectionParams() {
        Validator::validateFor('unique', '123', ['table'=>'table', 'column'=>'column']);
    }
}
