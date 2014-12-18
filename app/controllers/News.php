<?php

class News extends Controller{
    
    public function index(){

        $from=new Form('news', null, false);
        $from->addSelect('Seřadit (není součástí SP)', 'order', ['Název','Hodnocení','Rok','Doba trvání']);
        $from->check();
        
        $model = new CreationModel();
        $table = new Table();
        $table->loadDB($model, 'newsAll');
        $table->addColumn(['id', 'name_cs', 'release'], 'Název CZ', '<a href="' . URL . 'creation/{0}/"><strong>{1}</strong></a> ({2})');
        $table->addColumn('name_en', 'Název EN');
        
        
        $this->view->form=$from->generateHTML();
        $this->view->table=$table->generateHTML(false);
        $this->view->title = 'Novinky';
        $this->view->generatePage('news/index');
    }
    
}
