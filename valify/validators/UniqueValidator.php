<?php

namespace valify\validators;

/**
 * Class that checks specific value for uniqueness.
 * MySQL support only.
 *
 * Class UniqueValidator
 * @package valify\validators
 */
class UniqueValidator extends AbstractValidator {
    /**
     * An extended version of dsn - Extended DSN.
     * You can specify user name, password, table name and field name as well.
     * Usage example:
     * db=testdb;h=127.0.0.1;u=username;p=password;t=table_name;f=field_name
     *
     * @var $edsn
     */
    public $edsn;
    public $dbname;
    public $host;
    public $user;
    public $pass;
    public $table;
    public $column;
    public $message = '{value} already exists';

    /**
     * @var $conn \mysqli
     */
    private $conn;

    public function init() {
        if($this->edsn)
            $this->parseEDSN();

        if(!$this->table || !$this->column)
            throw new \UnexpectedValueException('Table and column must be specified');

        if( !is_string($this->table) || !is_string($this->column) )
            throw new \UnexpectedValueException('Table and column must be set as string');

        mysqli_report(MYSQLI_REPORT_STRICT);
        $mysqli = new \mysqli($this->host, $this->user, $this->pass, $this->dbname);

        if($mysqli->connect_errno)
            throw new \mysqli_sql_exception(printf("Connection error: %s\n", $mysqli->connect_error));

        $this->conn = $mysqli;

        parent::init();
    }

    protected function validateValue($value) {
        if( !$this->isPrintable($value) )
            $this->addError('Value is not a string');
        else {
            $value = empty($value) ? 'NULL' : "'" . $this->conn->real_escape_string($value) . "'";

            $sql = "SELECT count(*) as count from " . $this->table . " where " . $this->column . " = " . $value;

            $res = $this->conn->query($sql);
            $res = $res->fetch_object();

            if( (int)$res->count > 0 ) {
                $this->addError($this->message);
            }
        }
    }

    private function parseEDSN() {
        $edsn = explode(';', trim($this->edsn, ';'));

        foreach ($edsn as $param) {
            $name = substr($param, 0, strpos($param, '='));
            $value = substr($param, strpos($param, '=') + 1);

            switch($name) {
                case 'db':
                    $this->dbname = $value;
                    break;
                case 'h':
                    $this->host = $value;
                    break;
                case 'u':
                    $this->user = $value;
                    break;
                case 'p':
                    $this->pass = $value;
                    break;
                case 't':
                    $this->table = $value;
                    break;
                case 'c':
                    $this->column = $value;
                    break;
            }
        }
    }
}