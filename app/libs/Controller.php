<?php

class Controller {
    
    protected $view;
            
    function __construct() {
        Auth::checkLogin();
        $this->view = new View();
    }    
}
