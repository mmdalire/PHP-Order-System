<?php
class Login_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function getLoginUser($username, $password) {
        $query = $this->db->query(
            "SELECT * 
            FROM user 
            WHERE username = '" . $username . "' AND password = '" . $password . "';"
        );

        if(count($query->result()) == 1) {
            return (array) $query->row();
        }
        return NULL;
    }

    public function checkDuplicateUser($username) {
        $query = $this->db->query(
            "SELECT *
            FROM user
            WHERE username = '" . $username . "'
            LIMIT 1;"
        );

        if(count($query->result()) > 0) {
            return TRUE;
        } 
        return FALSE;
    }

    public function registerUser($firstName, $lastName, $userType, $password, $username, $status) {
        $query = $this->db->query(
            "INSERT INTO user (first_name, last_name, user_type_id, password, username, user_status_id, created_by, created_date)
            VALUES ('" . $firstName . "', '" . $lastName . "', " . $userType . ", '" . $password . "', '" . $username . "', " . $status . ", '" . $username . "', NOW());"
        );

        if($query) {
            return TRUE;
        }
        return FALSE;
    }
}