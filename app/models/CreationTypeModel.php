<?php

class CreationTypeModel extends Model{
   
    public function __construct() {
        parent::__construct();
        $this->table = 'creation_type';
    }
    
    public function getAll($parameters=null) {
        $this->db->sql("SELECT SQL_CALC_FOUND_ROWS `id`, `name` FROM creation_type ".$this->limit($parameters));
        $this->db->execute();

        $this->found = $this->db->foundRows();

        return $this->db->fetchAll();
    }
    
}
