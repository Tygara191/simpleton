<?php if(!defined("BASE_PATH")) die("Direct file access is forbidden.");

// NOTICE: Since we need our Config class to administer our configuration, we obviously cannot put
// The config files directory path here. It can be found and changed in the config class itself.

$config['base_url'] = "http://localhost/singleton/";

$config['dbopts']['db_host'] = "localhost";
$config['dbopts']['db_port'] = 3306;
$config['dbopts']['db_name'] = "test";
$config['dbopts']['db_user'] = "root";
$config['dbopts']['db_pass'] = "";

// First in line is considered the default language
$config['supported_languages'] = array(
    'en' => ['filename' => 'en.php', 'label' => 'English'],
    'bg' => ['filename' => 'bg.php', 'label' => 'Български'],
);

$config['languages_directory'] = BASE_PATH."/core/lang/";