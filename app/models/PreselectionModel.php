<?php

class PreselectionModel extends Model {

    public function __construct() {
        parent::__construct();
        $this->table = 'preselection';
    }

    public function getAll($parameters = null) {
        $this->db->sql("SELECT SQL_CALC_FOUND_ROWS preselection.`id`, preselection.`name` "
                . " FROM `preselection`"
                . " ORDER BY id " . $this->limit($parameters));
        $this->db->execute();

        $this->found = $this->db->foundRows();

        return $this->db->fetchAll();
    }

    public function get($parameters) {
        $this->db->sql("SELECT preselection.`id`, preselection.`name`, `release_from`, `release_to`, `runtime_from`, `runtime_to`, `rating_from`, `rating_to`,"
                . " GROUP_CONCAT(DISTINCT genre.name) AS genre,"
                . " GROUP_CONCAT(DISTINCT genre.id) AS genre_id,"
                . " creation_type.name AS type, creation_type.id AS type_id "
                . " FROM `preselection`"
                . " LEFT JOIN creation_type ON creation_type.id=preselection.creation_type_id"
                . " LEFT JOIN preselection_genre ON preselection_genre.preselection_id=preselection.id"
                . " LEFT JOIN genre ON genre.id=preselection_genre.genre_id"
                . " WHERE preselection.`id`=:id "
                . " GROUP BY preselection.`id`");
        $this->db->addParameter(':id', $parameters['id']);
        $this->db->execute();
        return $this->db->fetch();
    }

    public function add($parameters) {
        $this->db->sql("INSERT INTO preselection"
                . " (`name`, `release_from`, `release_to`, `runtime_from`, `runtime_to`, `rating_from`, `rating_to`, `creation_type_id`) "
                . " VALUES (:name,:release_from,:release_to,:runtime_from,:runtime_to,:rating_from,:rating_to,:type)");
        $this->db->addParameter(':name', $parameters['name']);
        $this->db->addParameter(':release_from', $parameters['release_from']);
        $this->db->addParameter(':release_to', $parameters['release_to']);
        $this->db->addParameter(':runtime_from', $parameters['runtime_from']);
        $this->db->addParameter(':runtime_to', $parameters['runtime_to']);
        $this->db->addParameter(':rating_from', $parameters['rating_from']);
        $this->db->addParameter(':rating_to', $parameters['rating_to']);
        $this->db->addParameter(':type', $parameters['type']);
        $this->db->execute();

        if (count($parameters['genre']) > 0)
            $this->editGenre($parameters['genre'], $this->db->lastID());

        return $this->db->rowCount();
    }

    public function edit($parameters) {
        $this->db->sql("UPDATE `preselection`"
                . " SET `name`=:name,"
                . " `release_from`=:release_from,"
                . " `release_to`=:release_to,"
                . " `runtime_from`=:runtime_from,"
                . " `runtime_to`=:runtime_to,"
                . " `rating_from`=:rating_from,"
                . " `rating_to`=:rating_to,"
                . " `creation_type_id`=:type "
                . " WHERE id=:id");
        $this->db->addParameter(':name', $parameters['name']);
        $this->db->addParameter(':release_from', $parameters['release_from']);
        $this->db->addParameter(':release_to', $parameters['release_to']);
        $this->db->addParameter(':runtime_from', $parameters['runtime_from']);
        $this->db->addParameter(':runtime_to', $parameters['runtime_to']);
        $this->db->addParameter(':rating_from', $parameters['rating_from']);
        $this->db->addParameter(':rating_to', $parameters['rating_to']);
        $this->db->addParameter(':type', $parameters['type']);
        $this->db->addParameter(':id', $parameters['id']);
        $this->db->execute();


        $this->editGenre($parameters['genre'], $parameters['id']);

        return $this->db->rowCount();
    }

    public function editGenre($genres, $preselectionID) {
        $this->db->sql("DELETE FROM preselection_genre WHERE preselection_id=:id");
        $this->db->addParameter(':id', $preselectionID);
        $this->db->execute();

        foreach ($genres as $genre) {
            $this->db->sql('INSERT INTO preselection_genre(`preselection_id`,`genre_id`) SELECT :id,genre.id FROM genre WHERE genre.name=:genre OR genre.id=:genre ');
            $this->db->addParameter(':id', $preselectionID);
            $this->db->addParameter(':genre', $genre);
            $this->db->execute();
        }
    }

    public function remove($parameters) {
        $this->db->sql("DELETE preselection_genre.*,preselection.*"
                . " FROM preselection"
                . " LEFT JOIN preselection_genre ON preselection_genre.preselection_id=preselection.id"
                . " WHERE preselection.id=:id");
        $this->db->addParameter(':id', $parameters['id']);
        $this->db->execute();
        return $this->db->rowCount();
    }

}
