<?php
defined("BASE_PATH") or define("BASE_PATH", "D:/xampp/htdocs/simpleton");

include BASE_PATH.'/core/config.php';

include BASE_PATH.'/core/language.php';
include BASE_PATH.'/core/db.php';

class Application{

    public $config;
    public $db;
    public $lang;
    public $auth;

    /**
     * Application constructor.
     * @param array $config
     */
    public function __construct($unauthenticated_only=false){
		$this->config = new Config([
		    'config.php'
        ]);
		$this->db = new DB($this->config);
		$this->lang = new Language($this->config);
		$this->auth = new Auth($this->db, $this->config);

		$this->checkAuthenticated($unauthenticated_only);
	}

    /**
     * @param bool $unauthenticated_only
     */
    private function checkAuthenticated($unauthenticated_only){

    }
}