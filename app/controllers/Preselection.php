<?php

class Preselection extends Controller {

    public function __construct() {
        parent::__construct();
        Auth::withLogin('operator');
    }

    public function survey() {

        $model = new PreselectionModel();
        $table = new Table();
        $table->loadDB($model, 'getAll');
        $table->addColumn('name', 'Název');
        $table->addColumn('id', '', '<a href="' . URL . 'preselection/edit/{0}/">Změnit</a>');
        $table->addColumn('id', '', '<a href="' . URL . 'preselection/remove/{0}/">Odstranit</a>');

        $this->view->tablePreselection = $table->generateHTML();

        $this->view->title = 'Předvolby';
        $this->view->setNavigation('account');
        $this->view->generatePage('preselection/survey');
    }

    public function add() {
        $form = new Form('preselection');
        $form->addField('Název', 'text', 'name')->setRequired(true)->setMinLength(2)->setMaxLength(45);

        $type = Form::optionsLoadDB((new CreationTypeModel())->getAll());
        $type[null] = 'Všechno';
        $form->addSelect('Typ', 'type', $type);

        $genre = Form::optionsLoadDB((new GenreModel())->getAll());
        $form->addMultiple('Žánr', 'genre', $genre);

        $form->addField('Rok od', 'number', 'release_from')->setMin(1950)->setMax(2020);
        $form->addField('Rok do', 'number', 'release_to')->setMin(1950)->setMax(2020);
        $form->addField('Délka od', 'number', 'runtime_from')->setMin(60)->setMax(500);
        $form->addField('Délka do', 'number', 'runtime_to')->setMin(60)->setMax(500);
        $form->addField('Hodnocení od', 'number', 'rating_from')->setMin(0)->setMax(100);
        $form->addField('Hodnocení do', 'number', 'rating_to')->setMin(0)->setMax(100);
        $form->addButton('Uložit');
        $form->check();

        if (!$form->isError) {
            $model = new PreselectionModel();
            $data = $form->getData();

            if ($model->exists(['name' => $data['name']])) {
                $form->setError('name', 'Tehle je použité.');
            }

            if ($data['release_from'] > $data['release_to']) {
                $form->setError('release_to', 'Rok má být od do.');
            }

            if ($data['runtime_from'] > $data['runtime_to']) {
                $form->setError('runtime_to', 'Délka má být od do.');
            }

            if ($data['rating_from'] > $data['rating_to']) {
                $form->setError('rating_to', 'Hodnocení má být od do.');
            }

            if (!$form->isError) {
                $model->transaction();
                if ($model->add($data) > 0) {
                    $model->commit();
                    Message::addSuccess('Vložení předvolby se povedlo.');
                    Redirect::to('preselection/survey/');
                } else {
                    $model->rollBack();
                    Message::addError('Nastala chyba při vložení předvolby.');
                }
            }
        }


        $this->view->formPreselection = $form->generateHTML();
        $this->view->title = 'Předvolby';
        $this->view->setNavigation('account');
        $this->view->generatePage('preselection/add');
    }

    public function edit($id = NULL) {
        $model = new PreselectionModel();
        $data = $model->get(['id' => $id]);

        if (empty($data) or count($data) == 0) {
            Message::addSuccess('Tato předvolba neexistuje.');
            Redirect::to('preselection/survey');
        }

        $form = new Form('preselection');
        $form->addField('Název', 'text', 'name', $data['name'])->setRequired(true)->setMinLength(2)->setMaxLength(45);

        $type = Form::optionsLoadDB((new CreationTypeModel())->getAll());
        $type[null] = 'Všechno';
        $form->addSelect('Typ', 'type', $type, $data['type_id']);

        $genre = Form::optionsLoadDB((new GenreModel())->getAll());
        $form->addMultiple('Žánr', 'genre', $genre, explode(',', $data['genre_id']));

        $form->addField('Rok od', 'number', 'release_from', $data['release_from'])->setMin(1950)->setMax(2020);
        $form->addField('Rok do', 'number', 'release_to', $data['release_to'])->setMin(1950)->setMax(2020);
        $form->addField('Délka od', 'number', 'runtime_from', $data['runtime_from'])->setMin(60)->setMax(500);
        $form->addField('Délka do', 'number', 'runtime_to', $data['runtime_to'])->setMin(60)->setMax(500);
        $form->addField('Hodnocení od', 'number', 'rating_from', $data['rating_from'])->setMin(0)->setMax(100);
        $form->addField('Hodnocení do', 'number', 'rating_to', $data['rating_to'])->setMin(0)->setMax(100);
        $form->addButton('Uložit');
        $form->check();

        if (!$form->isError) {
            $model = new PreselectionModel();
            $data = $form->getData();
            $data['id']=$id;
            
            if ($model->exists(['name' => $data['name']], ['id' => $data['id']])) {
                $form->setError('name', 'Tehle je použité.');
            }

            if ($data['release_from'] > $data['release_to']) {
                $form->setError('release_to', 'Rok má být od do.');
            }

            if ($data['runtime_from'] > $data['runtime_to']) {
                $form->setError('runtime_to', 'Délka má být od do.');
            }

            if ($data['rating_from'] > $data['rating_to']) {
                $form->setError('rating_to', 'Hodnocení má být od do.');
            }

            if (!$form->isError) {
                $model->transaction();
                if ($model->edit($data) > 0) {
                    $model->commit();
                    Message::addSuccess('Změnit předvolby se povedlo.');
                    Redirect::to('preselection/survey/');
                } else {
                    $model->rollBack();
                    Message::addError('Nastala chyba při změně předvolby.');
                }
            }
        }


        $this->view->formPreselection = $form->generateHTML();
        $this->view->title = 'Změnit předvolby';
        $this->view->setNavigation('account');
        $this->view->generatePage('preselection/edit');
    }

    public function remove($id=null) {
        
        $form = new Form('creation');
        $form->addButton('Ano', 'yes');
        $form->addButton('Ne', 'no');

        if ($form->isButton('yes')) {
            $model = new PreselectionModel();
            if ($model->remove(['id' => $id]) > 0) {
                Message::addSuccess('Povedlo se odstranit předvolbu.');
                Redirect::to('preselection/survey/');
            } else {
                Message::addError('Nastala chyba při odstranění předvolby.');
            }
        } elseif ($form->isButton('no')) {
            Redirect::to('preselection/survey/');
        }
        
        
        $this->view->form=$form->generateHTML();
        $this->view->title = 'Opravdu chcete odstranit předvolbu?';       
        $this->view->setNavigation('account');
        $this->view->generatePage('preselection/remove');
    }

}
