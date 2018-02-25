<?php if(!defined("BASE_PATH")) die("Direct file access is forbidden.");

class Language {
    private $defLang;
    private $lang;

    const ERROR_NO_LANGS_DEFINED = "Error: No languages defined. Please define some languages in config.";

    /**
     * Language constructor.
     * @param Config $config
     */
    public function __construct($config){
	    $languages = $config->item('supported_languages');
        if(count($languages) < 1){
            die(Language::ERROR_NO_LANGS_DEFINED);
        }
        $this->loadLanguage($languages, true);
	}

	private function loadLanguage($languages, $default=false){
//	    include();
//        if(){
//
//        }
    }

	private function getSelectedLanguage(){

    }

    private function getLanguageFromHeaders(){

    }

    private function setLanguage(){

    }

    private function suffixDatabaseField($fieldName){

    }

    private function get(){

    }
}
?>