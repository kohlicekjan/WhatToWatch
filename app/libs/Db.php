<?php

class Db extends PDO {

    private $stmt = null;

    public function __construct($host, $username, $password, $database, $type = DB_TYPE) {
        parent::__construct($type . ':host=' . $host . ';dbname=' . $database, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'", PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)); //PDO :: ATTR_PERSISTENT => TRUE, PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION
    }

    public static function defaultConnnect() {
        return new Db(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    }

    public function addParameter($key, $value) {

        if (empty($value)) {
            $type = PDO::PARAM_NULL;
        } elseif (is_int($value)) {
            $type = PDO::PARAM_INT;
        } elseif (is_bool($value)) {
            $type = PDO::PARAM_BOOL;
        } else {
            $type = PDO::PARAM_STR;
        }

        $this->stmt->bindParam($key, $value, $type);
    }

    public function sql($sql) {
        $this->stmt = parent::prepare($sql);
    }

    public function query($sql) {
        $this->stmt = parent::query($sql);
    }

    public function exec($sql) {
        return parent::exec($sql);
    }

    public function execute() {
        $this->stmt->execute();
    }

    public function rowCount() {
        return $this->stmt->rowCount();
    }

    public function fetchAll() {
        return $this->stmt->fetchAll(parent::FETCH_ASSOC);
    }

    public function fetch() {
        return $this->stmt->fetch(parent::FETCH_ASSOC);
    }

    public function foundRows() {
        return parent::query('SELECT FOUND_ROWS()')->fetch(parent::FETCH_NUM)[0];
    }

    public function result($field = 0) {
        $this->execute();
        return $this->stmt->fetch(parent::FETCH_NUM)[$field];
    }

    public function lastID() {
        return parent::lastInsertId();
    }

    public function exists() {
        if ($this->rowCount() == 0) {
            return false;
        } else {
            return true;
        }
    }

}