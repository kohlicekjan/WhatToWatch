<?php

class Model {

    public function __construct() {
        try{
        $this->db = Db::defaultConnnect();
        }catch(Exception $e){           
            App::Error(500);
        }
    }

    

}
