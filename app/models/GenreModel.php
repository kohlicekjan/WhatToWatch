<?php

class GenreModel extends Model {

    public function __construct() {
        parent::__construct();
        $this->table = 'genre';
    }

    public function getAll($parameters=null) {
        $this->db->sql("SELECT SQL_CALC_FOUND_ROWS `id`, `name` FROM `genre` ".$this->limit($parameters));
        $this->db->execute();

        $this->found = $this->db->foundRows();

        return $this->db->fetchAll();
    }


}
