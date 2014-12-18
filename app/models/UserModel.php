<?php

class UserModel extends Model {

    public function __construct() {
        parent::__construct();
        $this->table = 'user';
    }

    public function add($parameters) {
        $this->db->sql("INSERT INTO user "
                . " (username,password,email,`create`,active,disable,token,token_validity) "
                . " VALUES "
                . "(:username, :password, :email, NOW(), :active, :disable, :token, :tokenValidity)");
        $this->db->addParameter(':username', $parameters['username']);
        $this->db->addParameter(':password', Hash::make($parameters['passwd']));
        $this->db->addParameter(':email', $parameters['email']);
        $this->db->addParameter(':token', $parameters['token']);
        $this->db->addParameter(':tokenValidity', !empty($parameters['token']) ? date('Y-m-d H:i:s') : NULL);

        $this->db->addParameter(':disable', ($parameters['disable'] == 1) ? NULL : date('Y-m-d H:i:s'));
        $this->db->addParameter(':active', date('Y-m-d H:i:s'));
        $this->db->execute();

        $this->editRole(empty($parameters['role']) ? [2] : $parameters['role'], $this->db->lastID());

        return $this->db->rowCount();
    }

    public function login($parameters) {
        $this->db->sql("SELECT `user`.id,username,email,last_login,GROUP_CONCAT(role.name) AS role "
                . "FROM `user` "
                . "INNER JOIN user_role ON user_id=`user`.id "
                . "INNER JOIN role ON role_id=role.id "
                . "WHERE lower(username)=lower(:username) and password=:password "
                . "GROUP BY `user`.id "
                . "LIMIT 1");
        $this->db->addParameter(':username', $parameters['username']);
        $this->db->addParameter(':password', Hash::make($parameters['passwd']));
        $this->db->execute();
        $data = $this->db->fetch();

        if (!empty($data)) {
            $this->db->sql("UPDATE user SET last_login=NOW() WHERE id=:id");
            $this->db->addParameter(':id', $data['id']);
            $this->db->execute();
        }

        return $data;
    }

    public function active($token) {
        $this->db->sql("UPDATE user SET active=NOW(),token=NULL,token_validity=NULL WHERE token=:token");
        $this->db->addParameter(":token", $token);
        $this->db->execute();

        return $this->db->rowCount();
    }

    public function changePassword($parameters) {
        $this->db->sql("UPDATE user SET password=:password WHERE (:id IS NULL OR id=:id) OR (:token IS NULL OR token=:token)");
        $this->db->addParameter(":id", $parameters['id']);
        $this->db->addParameter(":token", $parameters['token']);
        $this->db->addParameter(":password", Hash::make($parameters['passwd']));
        $this->db->execute();

        return $this->db->rowCount();
    }

    public function edit($parameters) {

        $this->db->sql("UPDATE user SET username=:username, email=:email, disable=:disable WHERE id=:id ");
        $this->db->addParameter(':id', $parameters['id']);
        $this->db->addParameter(':username', $parameters['username']);
        $this->db->addParameter(':email', $parameters['email']);
        $this->db->addParameter(':disable', ($parameters['disable'] == 0) ? date('Y-m-d H:i:s') : NULL); //mozna problem s nulou
        $this->db->execute();

        $this->editRole($parameters['role'], $parameters['id']);



        return $this->db->rowCount();
    }

    public function editRole($roles, $userID) {

        $this->db->sql("DELETE FROM user_role WHERE user_id=:id");
        $this->db->addParameter(':id', $userID);
        $this->db->execute();

        foreach ($roles as $role) {
            $this->db->sql('INSERT INTO user_role(user_id,role_id) VALUES(:id,:role)');
            $this->db->addParameter(':id', $userID);
            $this->db->addParameter(':role', $role);
            $this->db->execute();
        }
    }

    public function get($parameters) {
        $this->db->sql("SELECT user.`id`, `username`, `email`,"
                . " `create`,  "
                . " `active`, "
                . " `disable`, "
                . " GROUP_CONCAT(role.name) AS role, "
                . " GROUP_CONCAT(role.id) AS role_id "
                . " FROM user "
                . " INNER JOIN user_role ON user_id=user.id "
                . " INNER JOIN role ON role_id=role.id "
                . " WHERE user.id=:id"
                . " GROUP BY user.id");
        $this->db->addParameter(':id', $parameters['id']);
        $this->db->execute();
        return $this->db->fetch();
    }

    public function getAll($parameters) {
        $this->db->sql("SELECT SQL_CALC_FOUND_ROWS `id`, `username`, `email`,"
                . " DATE_FORMAT(`create`,'%d.%m.%Y') as createDate,  "
                . " DATE_FORMAT(`active`,'%d.%m.%Y') as activeDate, "
                . " DATE_FORMAT(`disable`,'%d.%m.%Y') as disableDate "
                . " FROM user "
                . " WHERE id!=:id " . $this->limit($parameters));
        $this->db->addParameter(':id', Session::get('user')['id']);
        $this->db->execute();

        $this->found = $this->db->foundRows();

        return $this->db->fetchAll();
    }

}
