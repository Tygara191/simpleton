<?php if(!defined("BASE_PATH")) die("Direct file access is forbidden.");

class Config {
    /**
     * @var array
     */
    private $config;

    const ERROR_NO_CONFIG_FILE_FOUND = "Error: A configuration file was not found: <b>%s</b>";
    const ERROR_FILES_INCLUDED_BUT_NO_CONFIG_FOUND = "Error: Configuration files were included, but no \$config was found in them.";
    const ERROR_ITEM_NOT_FOUND = "Error: Configuration item not defined: <b>%s</b> but used at <b>%s</b> on line <b>%d</b>";

    /**
     * Config constructor.
     * @param array $config_files
     */
    public function __construct($config_files){
        $this->loadConfigurationFiles($config_files);
	}

    /**
     * @param string $key
     * @return mixed
     */
    public function item($key){
        if(!isset($this->config[$key])){
            die(sprintf(Config::ERROR_ITEM_NOT_FOUND, $key, debug_backtrace()[0]['file'], debug_backtrace()[0]['line']));
        }
        return $this->config[$key];
    }

    /**
     * @param array $config_files
     */
    private function loadConfigurationFiles($config_files){
        foreach($config_files as $config_file){
            if(!empty($config_file)){
                if(file_exists($config_file))
                    include($config_file);
                else
                    die(sprintf(Config::ERROR_NO_CONFIG_FILE_FOUND, $config_file));
            }
        }
        if(isset($config)) $this->config = $config; else die(Config::ERROR_FILES_INCLUDED_BUT_NO_CONFIG_FOUND);
    }
}