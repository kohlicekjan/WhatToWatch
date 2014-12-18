<?php

class ViewsModel extends Model {

    public function __construct() {
        parent::__construct();
        $this->table = 'preselection';
    }

    public function getAll($parameters) {
        $this->db->sql("SELECT SQL_CALC_FOUND_ROWS views.`id`,creation_id, `recommend`, `csfd_id`, `name_cs`, `name_en`, `release`, `runtime`, `rating`, `plot`, `poster_url`, creation_type.name AS type"
                . " FROM `views`"
                . " INNER JOIN creation ON creation.id=views.creation_id"
                . " INNER JOIN creation_type ON creation_type.id=creation.creation_type_id"
                . " WHERE (:user_id IS NULL OR user_id=:user_id)"
                . " AND (:recommend IS NULL OR views.recommend=:recommend) " . $this->limit($parameters));
        $this->db->addParameter(':user_id', $parameters['user_id']);
        $this->db->addParameter(':recommend', empty($parameters['recommend']) ? NULL : $parameters['recommend'] );
        $this->db->execute();

        $this->found = $this->db->foundRows();

        return $this->db->fetchAll();
    }

    public function add($parameters) {
        $this->db->sql("INSERT INTO `views`(`recommend`, `creation_id`, `user_id`) VALUES (:recommend,:creation_id,:user_id)");
        $this->db->addParameter(':user_id', $parameters['user_id']);
        $this->db->addParameter(':creation_id', $parameters['creation_id']);
        $this->db->addParameter(':recommend', empty($parameters['recommend']) ? 0 : $parameters['recommend']);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function edit($parameters) {
        $this->db->sql("UPDATE `views` SET `recommend`=IF(recommend='1', '0', '1') WHERE user_id=:user_id AND creation_id=:creation_id");
        $this->db->addParameter(':user_id', $parameters['user_id']);
        $this->db->addParameter(':creation_id', $parameters['creation_id']);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function remove($parameters) {
        $this->db->sql("DELETE FROM `views` WHERE user_id=:user_id AND creation_id=:creation_id");
        $this->db->addParameter(':user_id', $parameters['user_id']);
        $this->db->addParameter(':creation_id', $parameters['creation_id']);
        $this->db->execute();
        return $this->db->rowCount();
    }

}
