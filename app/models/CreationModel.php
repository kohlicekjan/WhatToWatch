<?php

class CreationModel extends Model {

    public function __construct() {
        parent::__construct();
        $this->table = 'creation';
    }

    public function add($parameters) {
        $this->db->sql("INSERT INTO `creation`(`csfd_id`, `name_cs`, `name_en`, `release`, `runtime`, `rating`, `plot`, `poster_url`, `creation_type_id`) "
                . " VALUES (:csfd_id, :name_cs, :name_en, :release, :runtime, :rating, :plot, :poster_url, :type)");
        $this->db->addParameter(':csfd_id', $parameters['csfd_id']);
        $this->db->addParameter(':name_cs', $parameters['name_cs']);
        $this->db->addParameter(':name_en', $parameters['name_en']);
        $this->db->addParameter(':release', $parameters['release']);
        $this->db->addParameter(':runtime', $parameters['runtime']);
        $this->db->addParameter(':rating', $parameters['rating']);
        $this->db->addParameter(':plot', $parameters['plot']);
        $this->db->addParameter(':poster_url', $parameters['poster_url']);
        $this->db->addParameter(':type', $parameters['type']);
        $this->db->execute();

        $this->editGenre($parameters['genre'], $this->db->lastID());

        return $this->db->rowCount();
    }

    public function editGenre($genres, $creationID) {
        $this->db->sql("DELETE FROM creation_genre WHERE creation_id=:id");
        $this->db->addParameter(':id', $creationID);
        $this->db->execute();

        foreach ($genres as $genre) {
            $this->db->sql('INSERT INTO creation_genre(`creation_id`,`genre_id`) SELECT :id,genre.id FROM genre WHERE genre.name=:genre ');
            $this->db->addParameter(':id', $creationID);
            $this->db->addParameter(':genre', $genre);
            $this->db->execute();
        }
    }

    public function edit($parameters) {
        $this->db->sql("UPDATE `creation`"
                . " SET `name_cs`=:name_cs,"
                . " `name_en`=:name_en,"
                . " `release`=:release,"
                . " `runtime`=:runtime,"
                . " `rating`=:rating,"
                . " `plot`=:plot,"
                . " `poster_url`=:poster_url "
                . " WHERE csfd_id=:csfd_id ");

        $this->db->addParameter(':csfd_id', $parameters['csfd_id']);
        $this->db->addParameter(':name_cs', $parameters['name_cs']);
        $this->db->addParameter(':name_en', $parameters['name_en']);
        $this->db->addParameter(':release', $parameters['release']);
        $this->db->addParameter(':runtime', $parameters['runtime']);
        $this->db->addParameter(':rating', $parameters['rating']);
        $this->db->addParameter(':plot', $parameters['plot']);
        $this->db->addParameter(':poster_url', $parameters['poster_url']);
        $this->db->execute();

        $this->editGenre($parameters['genre'], $parameters['id']);

        return $this->db->rowCount();
    }

    public function getAll($parameters = null) {
        $this->db->sql("SELECT SQL_CALC_FOUND_ROWS `creation`.`id`, `csfd_id`, `name_cs`, `name_en`, `release`, `runtime`, `rating`, `plot`, `poster_url`, `creation_type_id` "
                . " FROM `creation`"
                . " INNER JOIN creation_type ON creation_type.id=creation.creation_type_id "
                . " ORDER BY creation.id desc".$this->limit($parameters));
        $this->db->execute();

        $this->found = $this->db->foundRows();

        return $this->db->fetchAll();
    }

    public function get($parameters) {
        $this->db->sql("SELECT creation.id, csfd_id,`name_cs`, `name_en`, `release`, `runtime`, `rating`, `plot`, `poster_url`,"
                . " creation_type.name AS type, "
                . " GROUP_CONCAT(DISTINCT genre.name) AS genre,"
                . " views.id AS views, views.recommend "
                . " FROM creation"
                . " INNER JOIN creation_type ON creation_type.id=creation.creation_type_id"
                . " LEFT JOIN creation_genre ON creation_genre.creation_id=creation.id"
                . " LEFT JOIN genre ON creation_genre.genre_id=genre.id"
                . " LEFT JOIN views ON views.creation_id=creation.id and views.user_id=:user_id "
                . " WHERE creation.id=:id"
                . " GROUP BY creation.id");

        $this->db->addParameter(':id', $parameters['id']);
        $this->db->addParameter(':user_id', $parameters['user_id']);
        $this->db->execute();
        return $this->db->fetch();
    }

    public function remove($parameters) {
        $this->db->sql("DELETE creation_genre.*,views.*,creation.* "
                . " FROM creation"
                . " LEFT JOIN creation_genre ON creation_genre.creation_id=creation.id"
                . " LEFT JOIN views ON views.creation_id=creation.id"
                . " WHERE creation.id=:id");
        $this->db->addParameter(':id', $parameters['id']);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function random($parameters) {
        $this->db->sql("SELECT
                        creation.id
                        FROM creation               
                        INNER JOIN preselection ON preselection.id=:preselection
                        
                        WHERE
                        creation.release<=YEAR(CURDATE())
                        AND (preselection.runtime_from IS NULL OR creation.runtime>=preselection.runtime_from) 
                        AND (preselection.runtime_to IS NULL OR creation.runtime<=preselection.runtime_to)
                        AND  (preselection.creation_type_id IS NULL OR creation.creation_type_id=preselection.creation_type_id)
                        AND (preselection.release_from IS NULL OR creation.release>=preselection.release_from) 
                        AND (preselection.release_to IS NULL OR creation.release<=preselection.release_to)
                        AND (preselection.rating_from IS NULL OR creation.rating>=preselection.rating_from) 
                        AND (preselection.rating_from IS NULL OR creation.rating<=preselection.rating_to)
                        
                        AND ( 
                        NOT EXISTS (

                        SELECT * FROM 
                        preselection_genre 
                        WHERE 
                        preselection_id=preselection.id

                        ) OR  EXISTS (

                        SELECT * FROM 
                        preselection_genre 
                        INNER JOIN creation_genre ON creation_genre.genre_id=preselection_genre.genre_id
                        WHERE 
                        creation_genre.creation_id=creation.id AND preselection_genre.preselection_id= preselection.id
                        ) 
                        )
                        AND (:user_id IS NULL OR NOT EXISTS(SELECT views.id FROM views WHERE views.user_id=:user_id AND views.creation_id=creation.id) )    

                        ORDER BY RAND()*RAND() DESC LIMIT 1");
        $this->db->addParameter(':preselection', $parameters['preselection']);
        $this->db->addParameter(':user_id', $parameters['user_id']);
        $this->db->execute();
        return $this->db->fetch();
    }

    public function recommendAll($parameters=null){
        $this->db->sql("SELECT sum(recommend) AS re, `creation`.`id`, `csfd_id`, `name_cs`, `name_en`, `release`, `runtime`, `rating`, `plot`, `poster_url`, `creation_type_id` "
                . " FROM `creation`"
                . " INNER JOIN creation_type ON creation_type.id=creation.creation_type_id "
                . " INNER JOIN views ON creation_id=creation.id "
                . " GROUP BY creation.id "
                . " HAVING re>0 "
                . " ORDER BY re desc, name_cs "
                . " LIMIT 25 ");
        $this->db->execute();

        $this->found = 25;
        
        return $this->db->fetchAll();
    }
    
    public function newsAll($parameters=null){
        $this->db->sql("SELECT  `creation`.`id`, `csfd_id`, `name_cs`, `name_en`, `release`, `runtime`, `rating`, `plot`, `poster_url`, `creation_type_id` "
                . " FROM `creation`"
                . " INNER JOIN creation_type ON creation_type.id=creation.creation_type_id "
                . " ORDER BY creation.id desc "
                . " LIMIT 25 ");
        $this->db->execute();

        $this->found = 25;
        
        return $this->db->fetchAll();
    }
    
    
}
