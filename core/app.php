<?php
include 'config.php';
include 'db.php';

class Application{
	public function __construct($config){
		$this->config = $config;
		$this->db = new DB($config['dbopts']);
	}
}