<?php

class Index extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $model=new PreselectionModel();  
        $form = new Form('preselection','creation/random/',false); 
        $form->addSelect('Předvolby','type',Form::optionsLoadDB($model->getAll()))->setRequired(true);
        $form->addButton('WhatToWatch', null,' class="bigButton"');
        $form->check();
   
        $this->view->form = $form->generateHTML();
        $this->view->title = 'Vítejte na WhatToWatch';
        $this->view->generatePage('index/index');
    }
   
}
