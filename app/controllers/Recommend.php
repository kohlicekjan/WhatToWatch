<?php

class Recommend extends Controller{
   
    public function __construct() {
        parent::__construct();
    }
    
    public function index(){
        
        $from=new Form('recommend', null, false);
        $from->addSelect('Seřadit (není součástí SP)', 'order', ['Název','Hodnocení','Rok','Doba trvání']);
        $from->check();
        
        $model = new CreationModel();
        $table = new Table();
        $table->loadDB($model, 'recommendAll');
        $table->addColumn(['id', 'name_cs', 'release'], 'Název CZ', '<a href="' . URL . 'creation/{0}/"><strong>{1}</strong></a> ({2})');
        $table->addColumn('name_en', 'Název EN');
        
        $this->view->form=$from->generateHTML();
        $this->view->table=$table->generateHTML(false);
        $this->view->title = 'Doporučení';  
        $this->view->generatePage('recommend/index');
    }
    
    public function survey(){
        Auth::withLogin('user');
        
        $model = new ViewsModel();
        $table = new Table();
        $table->loadDB($model, 'getAll' ,['user_id'=>Session::get('user')['id'],'recommend'=>1]);
        $table->addColumn('type', 'Typ');
        $table->addColumn(['creation_id','name_cs','release'], 'Název CZ','<a href="' . URL . 'creation/{0}/"><strong>{1}</strong></a> ({2})');
        $table->addColumn('name_en', 'Název EN');
 
        $this->view->title = 'Doporučení';
        $this->view->tableRecommend=$table->generateHTML();
        $this->view->setNavigation('account');
        $this->view->generatePage('recommend/survey');
    }
    
}
