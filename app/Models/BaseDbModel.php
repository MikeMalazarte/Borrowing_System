<?php namespace App\Models;

use CodeIgniter\Model;

class BaseDBModel extends Model {

    protected $db;
    protected $request;
    protected $session;

    public function __construct() {
        parent::__construct();
        $this->db      = \Config\Database::connect();
        $this->request = \Config\Services::request();
        $this->session = session();
    }

    public function exec($sql) {
        return $this->db->query($sql);
    }

    public function currentUser() {
        return $this->session->get('user_code');
    }

    public function currentRole() {
        return $this->session->get('user_role');
    }

} // end BaseDBModel