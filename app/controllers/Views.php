<?php

class Views extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function survey() {
        Auth::withLogin('user');
        
        $model = new ViewsModel();
        $table = new Table();
        $table->loadDB($model, 'getAll', ['user_id' => Session::get('user')['id']]);
        $table->addColumn('type', 'Typ');
        $table->addColumn(['creation_id','name_cs','release'], 'Název CZ','<a href="' . URL . 'creation/{0}/"><strong>{1}</strong></a> ({2})');
        $table->addColumn('name_en', 'Název EN');

        $this->view->title = 'Zhlédnutí';
        $this->view->tableViews = $table->generateHTML();
        $this->view->setNavigation('account');
        $this->view->generatePage('views/survey');
    }

}
