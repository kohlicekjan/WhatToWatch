<?php

class Model {

    protected $db = null;
    protected $table;

    public function __construct() {
        try {
            $this->db = Db::defaultConnnect();
        } catch (Exception $e) {
            App::Error(500);
        }
    }

    public function transaction(){
        $this->db->beginTransaction();
    }


    public function commit(){
        $this->db->commit();
    }
    
    public function rollBack(){
        $this->db->rollBack();
    }
    
    public function exists($equal=[],$unequal=[]) {
        $where = [];

        foreach ($equal as $key => $value) {
            if ($value == 'NULL') {
                $where[] = '(' . $key . ' IS ' . $value . ')';
                unset($equal[$key]);
            } else {
                $where[] = '(' . $key . '=:' . $key . ')';
            }
        }
        
        foreach ($unequal as $key => $value) {
            if ($value == 'NULL') {
                $where[] = '(' . $key . ' IS NOT ' . $value . ')';
                unset($unequal[$key]);
            } else {
                $where[] = '(' . $key . '!=:' . $key . ')';
            }
        }

        $this->db->sql("SELECT id FROM ".$this->table." WHERE " . implode(' and ', $where));
        foreach ($equal as $key => $value) {
            $this->db->addParameter(':' . $key, $value);
        }
        foreach ($unequal as $key => $value) {
            $this->db->addParameter(':' . $key, $value);
        }

        $this->db->execute();
        return $this->db->exists();
    }
    
    protected function limit($parameters){
        if(empty($parameters['limit'])){
            return "";
        }elseif(empty($parameters['start'])){
            return " LIMIT ".intval($parameters['limit']);
        }else{
            return " LIMIT ".intval($parameters['start']).",".intval($parameters['limit']);
        }
    }
    
    
}
