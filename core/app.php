<?php

defined("BASE_PATH") or define("BASE_PATH", "D:/xampp/htdocs/simpleton");

class Application{

    const CONFIG_FILES_LOCATION = BASE_PATH."/core/config/";

    public $config;
    public $db;
    public $lang;
    public $auth;
    public $validation;

    /**
     * Application constructor.
     */
    public function __construct($unauthenticated_only=false){
        // Put these here so we do not clutter the global namespace
        include BASE_PATH . '/core/config.class.php';
        include BASE_PATH . '/core/language.class.php';
        include BASE_PATH . '/core/databaseManager.class.php';
        include BASE_PATH . '/core/encryption.class.php';
        include BASE_PATH . '/core/auth.class.php';
        include BASE_PATH . '/core/gump.class.php';

		$this->config = new Config([
            Application::CONFIG_FILES_LOCATION.'main.conf.php',
        ]);
		$this->db = new DatabaseManager($this->config);
		$this->lang = new Language($this->config);
		$this->encryption = new Encryption($this->config);
		$this->auth = new Auth($this->config, $this->db, $this->encryption);
        $this->validation = new GUMP();

        // Sanitization. See more at gump docs @ https://github.com/Wixel/GUMP
        $_POST = $this->validation->sanitize($_POST);
        $_GET = $this->validation->sanitize($_GET);

		$this->checkAuthenticated($unauthenticated_only);
	}

    /**
     * @param bool $unauthenticated_only
     */
    private function checkAuthenticated($unauthenticated_only){

    }
}