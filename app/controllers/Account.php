<?php

class Account extends Controller {

    public function index() {
        Auth::withLogin();
        
        $model=new UserModel();
        $data=$model->get(['id'=>Session::get('user')['id']]);
        
        $this->view->email =$data['email'];
        $this->view;
        
        
        $this->view->title = $data['username'];
        $this->view->setNavigation('account');
        $this->view->generatePage('account/index');
    }

    //nesmim byt prihlasen
    public function login($url = null) {
        Auth::withoutLogin();

        $form = new Form('login');
        $form->addField('Uživatelské jméno', 'text', 'username')->setRequired(true);
        $form->addField('Heslo', 'password', 'passwd')->setRequired(true);
        $form->addButton('Přihlásit');
        $form->addButton('Zapomněli jste heslo?', URL . 'account/recovery/', 'link');
        $form->check();

        if (!$form->isError) {
            $data = $form->getData();
            $model = new UserModel();
            $row = $model->login($data);
            if (!empty($row) and count($row) > 0) {
                if ($model->exists(['username' => $data['username'], 'active' => 'NULL'])) {
                    Message::addWarning('Musí te aktivovat učet.');
                } elseif ($model->exists(['username' => $data['username']], ['disable' => 'NULL'])) {
                    Message::addWarning('Máte blokovaný účet, kontaktujte admina.');
                } else {
                    Auth::login($row, $url);
                }
            } else {
                $form->setError('passwd', 'Zadané uživatelské jméno nebo heslo je nesprávné.');
            }
        }
        $this->view->form = $form->generateHTML();
        $this->view->title = 'Přihlásit se';
        $this->view->generatePage('account/login');
    }

    public function logout($url = null) {
        Auth::logout($url);
    }

    public function active($token = null) {
        Auth::withoutLogin();

        if (!empty($token) and (bool) (new UserModel())->active(Hash::decrypt($token))) {
            Message::addSuccess('Účet je aktivován můžete se přihlásit.');
        } else {
            Message::addError('Učet se nepovedlo aktivovat.');
        }

        Redirect::to('account/login/');
    }

    public function recovery() {
        Auth::withoutLogin();

        $form = new Form('recovery');
        $form->addField('Email(neni soucastí SP)', 'email', 'email')->setRequired(true);
        $form->addField('Kontrolní otázka: ', 'text', 'question')->setRequired(true);
        $form->addButton('Odeslat');
        $form->check();

        $this->view->form = $form->generateHTML();
        $this->view->title = 'Zapomenutí hesla';
        $this->view->generatePage('account/recovery');
    }

    //nesmim byt prihlasen
    public function signUp() {
        Auth::withoutLogin();

        $form = new Form('singup');
        $form->addField('Uživatelské jméno', 'text', 'username')->setRequired(true)->setMinLength(2)->setMaxLength(30)->setPattern('[A-Za-z0-9]+');
        $form->addField('Email', 'email', 'email')->setRequired(true)->setMinLength(4)->setMaxLength(45);
        $form->addField('Vytvořte heslo', 'password', 'passwd')->setRequired(true)->setMinLength(6);
        $form->addField('Potvrďte heslo', 'password', 'passwdAgain')->setRequired(true)->setMinLength(6);
        $form->addButton('Vytvořit');
        $form->check();

        if (!$form->isError) {
            $data = $form->getData();
            if ($data['passwd'] != $data['passwdAgain']) {
                $form->setError('passwdAgain', 'Heslo se neschodují.');
            }

            $model = new UserModel();

            if ($model->exists(['username' => $data['username']])) {
                $form->setError('username', 'Tohle uživatelské jméno je použité.');
            }

            if ($model->exists(['email' => $data['email']])) {
                $form->setError('email', 'Tehle email je použitý.');
            }

            if (!$form->isError) {
                $token = Auth::randomKey();
                $data['token'] = Auth::randomKey();
                $data['disable']=1;
                
                $model->transaction();
                
                //ODESLANI SMS                    
                $mail = new Mail();
                $mail->from = 'noreply@domena.cz';
                $mail->fromName = 'Noreply';
                $mail->subjects = 'Vytvoření účtu';
                $mail->message = 'Dobrý den,'
                        . '';



                //ODESLANI SMS


                if ($model->add($data) > 0) { //and $mail->send()) {
                    $model->commit();
                    Message::addSuccess('Vytvoření nového účtu se povedlo.');
                    Redirect::to('account/login/');
                } else {
                    $model->rollBack();
                    Message::addError('Nastala chyba při vytváření účtu.');
                }
            }
        }

        $this->view->form = $form->generateHTML();
        $this->view->title = 'Vytvořit účet';
        $this->view->generatePage('account/signup');
    }

}
