<?php

class User extends Controller {

    public function __construct() {
        parent::__construct();
        Auth::withLogin('administrator');
    }

    public function survey() {
        $model = new UserModel();
        $table = new Table();
        $table->loadDB($model, 'getAll');
        $table->addColumn('username', 'Uživatelské jméno');
        $table->addColumn('email', 'Email');
        $table->addColumn('createDate', 'Založení účtu');
        $table->addColumn('activeDate', 'Aktivován');
        $table->addColumn('disableDate', 'Blokován');
        $table->addColumn('id', '', '<a href="' . URL . 'user/edit/{0}/">Změnit</a>');

        $this->view->tableUser = $table->generateHTML();
        $this->view->title = 'Uživatelé';
        $this->view->setNavigation('account');
        $this->view->generatePage('user/survey');
    }

    public function add() {
        $form = new Form('user');
        $form->addField('Uživatelské jméno', 'text', 'username')->setRequired(true)->setMinLength(2)->setMaxLength(30)->setPattern('[A-Za-z0-9]+');
        $form->addField('Email', 'email', 'email')->setRequired(true)->setMinLength(4)->setMaxLength(45);
        $form->addSelect('Blokován', 'disable', ['Ano', 'Ne'])->setRequired(true);

        $role = Form::optionsLoadDB((new RoleModel())->getAll());
        $form->addMultiple('Role', 'role', $role)->setRequired(true);
        $form->addField('Vytvořte nové heslo', 'password', 'passwd')->setRequired(true)->setMinLength(6);
        $form->addField('Potvrďte nové heslo', 'password', 'passwdAgain')->setRequired(true)->setMinLength(6);
        $form->addButton('Uložit');
        $form->check();

        if (!$form->isError) {
            $data = $form->getData();
            $model = new UserModel();
            $model->transaction();
            
            if ($data['passwd'] != $data['passwdAgain']) {
                    $form->setError('passwdAgain', 'Heslo se neschodují.');
            }         

            if ($model->exists(['username' => $data['username']])) {
                $form->setError('username', 'Tohle uživatelské jméno je použité.');
            }

            if ($model->exists(['email' => $data['email']])) {
                $form->setError('email', 'Tehle email je použitý.');
            }

            if (!$form->isError) {
                if ($model->add($data)>0) {
                    $model->commit();
                    Message::addSuccess('Vytvoření nového účtu se povedlo.');
                    Redirect::to('user/survey/');
                } else {
                    $model->rollBack();
                    Message::addError('Nastala chyba při vytváření účtu.');
                }
            }else{
                $model->rollBack();
            }
        }


        $this->view->formUser = $form->generateHTML();
        $this->view->title = 'Přidat uživatele';
        $this->view->setNavigation('account');
        $this->view->generatePage('user/add');
    }

    public function edit($id = null) {
        $model = new UserModel();
        $data = $model->get(['id' => $id]);

        if(empty($data) or count($data)==0){
             Message::addSuccess('Tento účet neexistuje.');
             Redirect::to('user/survey');
        }
        
        $form = new Form('user');
        $form->addField('Uživatelské jméno', 'text', 'username', $data['username'])->setRequired(true)->setMinLength(2)->setMaxLength(30)->setPattern('[A-Za-z0-9]+');
        $form->addField('Email', 'email', 'email', $data['email'])->setRequired(true)->setMinLength(4)->setMaxLength(45);
        $form->addSelect('Blokován', 'disable', ['Ano', 'Ne'], empty($data['disable']))->setRequired(true);

        $role = Form::optionsLoadDB((new RoleModel())->getAll());
        $form->addMultiple('Role', 'role', $role, explode(',', $data['role_id']))->setRequired(true);
        $form->addField('Vytvořte nové heslo', 'password', 'passwd')->setMinLength(6);
        $form->addField('Potvrďte nové heslo', 'password', 'passwdAgain')->setMinLength(6);
        $form->addButton('Uložit');
        $form->check();


        if (!$form->isError) {
            $data = $form->getData();
            $data['id'] = $id;

            $model = new UserModel();
            $model->transaction();
            if (!empty($data['passwd'])) {

                if (empty($data['passwdAgain'])) {
                    $form->setError('passwdAgain', 'Zahejte heslo znovu.');
                } elseif ($data['passwd'] != $data['passwdAgain']) {
                    $form->setError('passwdAgain', 'Heslo se neschodují.');
                }
                $model->changePassword($data);
            }

            if ($model->exists(['username' => $data['username']],['id'=>$data['id']])) {
                $form->setError('username', 'Tohle uživatelské jméno je použité.');
            }

            if ($model->exists(['email' => $data['email']],['id'=>$data['id']])) {
                $form->setError('email', 'Tehle email je použitý.');
            }

            if (!$form->isError) {
                if ($model->edit($data)>0) {
                    $model->commit();
                    Message::addSuccess('Změna účtu se povedla.');
                    Redirect::to('user/survey/');
                } else {
                    $model->rollBack();
                    Message::addError('Nastala chyba při změně účtu.');
                }
            }else{
                $model->rollBack();
            }
        }

        $this->view->title = 'Změnit uživatele';
        $this->view->formUser = $form->generateHTML();
        $this->view->setNavigation('account');
        $this->view->generatePage('user/edit');
    }

}
