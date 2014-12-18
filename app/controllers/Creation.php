<?php

class Creation extends Controller {

    public function index($id = null) {
        $model = new CreationModel();
        $data = $model->get(['id' => $id, 'user_id' => Session::get('user')['id']]);
        $this->view->formViews = '';

        if (!empty($data)) {
            if (Auth::isLogin('user')) {
                $formViews = new Form('views');
                $formViews->addButton(empty($data['views']) ? 'Zkouknuto' : 'Zrušit zhlédnutí', 'views', ' class="normalButton greenBG"');
                $formViews->addButton((empty($data['recommend']) or $data['recommend'] == 0) ? 'Doporučit' : 'Zrušit doporučení', 'recommend', ' class="normalButton yellowBG"');


                if ($formViews->isButton('views')) {
                    $model = new ViewsModel();

                    if (empty($data['views'])) {
                        $method = 'add';
                    } else {
                        $method = 'remove';
                    }

                    if ($model->$method(['creation_id' => $id, 'user_id' => Session::get('user')['id']]) > 0) {
                        Message::addSuccess('Dílo je zhlédnuté.');
                        Redirect::to();
                    } else {
                        Message::addError('Nastala chyba při zhlédnutí díla.');
                    }
                }

                if ($formViews->isButton('recommend')) {
                    $model = new ViewsModel();

                    if (empty($data['views'])) {
                        $method = 'add';
                    } else {
                        $method = 'edit';
                    }

                    if ($model->$method(['creation_id' => $id, 'user_id' => Session::get('user')['id'], 'recommend' => 1]) > 0) {
                        Message::addSuccess('Dílo je zhlédnuté a doporučené.');
                        Redirect::to();
                    } else {
                        Message::addError('Nastala chyba při zhlédnutí a doporučení díla.');
                    }
                }


                $this->view->formViews = $formViews->generateHTML();
            }

            $this->view->csfd_id = $data['csfd_id'];
            $this->view->name_cs = $data['name_cs'];
            $this->view->name_en = $data['name_en'];
            $this->view->release = $data['release'];
            $this->view->runtime = $data['runtime'];
            $this->view->rating = $data['rating'];
            $this->view->plot = $data['plot'];
            $this->view->poster_url = str_replace('?h180', '', $data['poster_url']);
            $this->view->type = $data['type'];
            $this->view->genre = str_replace(',', ' / ', $data['genre']);

            $this->view->title = $data['name_cs'];
        }
        $form = new Form('preselection', 'creation/random/', false);
        $form->addSelect('Předvolby', 'type', Form::optionsLoadDB((new PreselectionModel())->getAll()));
        $form->addButton('WhatToWatch', null, ' class="bigButton"');
        $form->check();

        $this->view->form = $form->generateHTML();
        $this->view->generatePage('creation/index');
    }

    public function random() {
        $model = new CreationModel();

        if (!empty($_GET['preselection']['type']) and is_numeric($_GET['preselection']['type'])) {
            $preselection = $_GET['preselection']['type'];
            $data = $model->random(['preselection' => $preselection, 'user_id' => Session::get('user')['id']]);
        }
        $url = 'creation/' . (empty($data['id']) ? '' : $data['id'] . '/?preselection%5Dtype%5D=' . $_GET['preselection']['type']);
        Redirect::to($url);
    }

    public function survey() {
        Auth::withLogin('editor');
        
        $model = new CreationModel();
        $table = new Table();
        $table->loadDB($model, 'getAll');
        $table->addColumn('csfd_id', 'ID ČSFD');
        $table->addColumn(['id', 'name_cs', 'release'], 'Název CZ', '<a href="' . URL . 'creation/{0}/"><strong>{1}</strong></a> ({2})');
        $table->addColumn('name_en', 'Název EN');

        $table->addColumn('id', '', '<a href="' . URL . 'creation/edit/{0}/">Aktualizovat</a>');
        $table->addColumn('id', '', '<a href="' . URL . 'creation/remove/{0}/">Odstranit</a>');

        $this->view->tableUser = $table->generateHTML();
        $this->view->title = 'Díla';
        $this->view->setNavigation('account');
        $this->view->generatePage('creation/survey');
    }

    public function add() {
        Auth::withLogin('editor');
        $form = new Form('creation');
        $form->addField('ID ČSFD', 'number', 'csfd_id')->setRequired(true);
        $form->addButton('Načíst', 'load');
        $form->check();

        if (!$form->isError) {

            $data = CSFD::loadMovie($form->getData()['csfd_id']);
            if (count($data) == 0) {
                $form->setError('csfd_id', 'Tohle to dílo neexistuje v databázi ČSFD');
            } else {
                $form->addInfo('Název CZ', 'name_cs', $data['name_cs']);
                $form->addInfo('Název EN', 'name_en', $data['name_en']);
                $form->addInfo('Rok', 'release', $data['release']);

                $model = new CreationModel();
                if ($model->exists(['csfd_id' => $data['csfd_id']])) {
                    $form->setError('csfd_id', 'Tohle to dílo je již v databázi.');
                }
            }

            if (!$form->isError) {
                $form->addButton('Uložit', 'save');
                if ($form->isButton('save')) {
                    $model->transaction();
                    if ($model->add($data) > 0) {
                        $model->commit();
                        Message::addSuccess('Vložení díla se povedlo.');
                        Redirect::to('creation/survey/');
                    } else {
                        $model->rollBack();
                        Message::addError('Nastala chyba při vkládání díla.');
                    }
                }
            }
        }

        $this->view->formCreation = $form->generateHTML();
        $this->view->title = 'Přidat dílo';
        $this->view->setNavigation('account');
        $this->view->generatePage('creation/add');
    }

    public function edit($id = null) {
        Auth::withLogin('editor');
        
        $model = new CreationModel();
        $data = $model->get(['id' => $id]);

        if (empty($data) or count($data) == 0) {
            Message::addSuccess('Toto dílo neexistuje.');
            Redirect::to('creation/survey/');
        }

        $form = new Form('creation');
        $form->addInfo('Název CZ', 'name_cs', $data['name_cs']);
        $form->addInfo('Název EN', 'name_en', $data['name_en']);
        $form->addInfo('Rok', 'release', $data['release']);
        $data = CSFD::loadMovie($data['csfd_id']);
        if (count($data) != 0) {
            $form->addInfo('Nový název CZ', 'name_cs_new', $data['name_cs']);
            $form->addInfo('Nový název EN', 'name_en_new', $data['name_en']);
            $form->addInfo('Nový rok', 'release_new', $data['release']);
        }

        $form->addButton('Aktualizovat', 'update');
        $form->check();

        if (!$form->isError) {
            $model->transaction();
            $data['id'] = $id;
            if ($model->edit($data) > 0) {
                $model->commit();
                Message::addSuccess('Aktualizace díla se povedlo.');
                Redirect::to('creation/survey/');
            } else {
                $model->rollBack();
                Message::addError('Nastala chyba při vkládání díla.');
            }
        }
        $this->view->formCreation = $form->generateHTML();
        $this->view->title = 'Aktualizace díla';
        $this->view->setNavigation('account');
        $this->view->generatePage('creation/edit');
    }

    public function remove($id = null) {
        Auth::withLogin('editor');
        
        $form = new Form('creation');
        $form->addButton('Ano', 'yes');
        $form->addButton('Ne', 'no');

        if ($form->isButton('yes')) {
            $model = new CreationModel();
            if ($model->remove(['id' => $id]) > 0) {
                Message::addSuccess('Povedlo se odstranit dílo.');
                Redirect::to('creation/survey/');
            } else {
                Message::addError('Nastala chyba při odstranění díla.');
            }
        } elseif ($form->isButton('no')) {
            Redirect::to('creation/survey/');
        }

        $this->view->form = $form->generateHTML();
        $this->view->title = 'Opravdu chcete odstranit dílo?';
        $this->view->setNavigation('account');
        $this->view->generatePage('creation/remove');
    }

}
