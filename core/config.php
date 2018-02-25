<?php

$config['base_url'] = "http://localhost/singleton/";

$config['dbopts']['db_host'] = "localhost";
$config['dbopts']['db_port'] = 3306;
$config['dbopts']['db_name'] = "analyze_me";
$config['dbopts']['db_user'] = "root";
$config['dbopts']['db_pass'] = "";

// First in line is considered the default language
$config['supported_languages'] = array(
	['code' => 'en', 'label' => 'Английски'],
	['code' => 'bg', 'label' => 'Български'],
);

?>