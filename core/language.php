<?php if(!defined("BASE_PATH")) die("Direct file access is forbidden.");

class Language {
    private $config;

    /**
     * array @var
     */
    private $defLang;
    /**
     * array @var
     */
    private $currentLang;
    private $languages;

    const ERROR_NO_LANGS_DEFINED = "Error: No languages defined. Please define some languages in config.";
    const ERROR_NO_VALUE = "Error: No languages value defined for: <b>%s</b>, used at <b>%s</b> on line <b>%d</b>";
    const ERROR_NO_FILE = "Error: Language <b>%s</b> defined in config, but not found at <b>%s</b>";
    const ERROR_BAD_KEY = "Error: You called Language::loadLanguage() with bad \$key <b>%s</b> at <b>%s</b> on line <b>%d</b>";
    const ERROR_BAD_LANG_CONFIG = "Error: Improperly configured language <b>%s</b>. Check documentation for how to configure languages.";
    const ERROR_NO_LANG_IN_FILE = "Error: No \$lang array was found in <b>%s</b>. Please define language file as said in docs.";
    const ERROR_LANGUAGES_NOT_LOADED = "Error: For some reason the languages weren't loaded properly. Sadly that's just about all we know...";

    /**
     * Language constructor.
     * @param Config $config
     */
    public function __construct($config){
        $this->config = $config;
	    $languages = $this->config->item('supported_languages');
        if(!is_array($languages) || count($languages) < 1)
            die(Language::ERROR_NO_LANGS_DEFINED);
        $this->languages = $languages;

        $default_language_key = $this->getDefaultLanguageKey();

        $this->defLang = $this->loadLanguage($default_language_key);

        if($this->getSelectedLanguage() == false){
            // Header call here

            // If header call also fails
            $this->currentLang = $this->defLang;
        }else{
            if($this->isValidLanguageKey($this->getSelectedLanguage())){
                $this->currentLang = $this->loadLanguage($this->getSelectedLanguage());
            }else{
                $this->currentLang = $this->defLang;
            }
        }
	}

    /**
     * @param array $languages
     * @param bool|string $key
     * @param bool $default
     * @return array
     */
    private function loadLanguage($key=false){
        if($key == false || !$this->isValidLanguageKey($key))
            die(sprintf(Language::ERROR_BAD_KEY, $key, debug_backtrace()[0]['file'], debug_backtrace()[0]['line']));
        $lang_item = $this->languages[$key];

        if(!isset($lang_item['filename']) || !isset($lang_item['label']))
            die(sprintf(Language::ERROR_BAD_LANG_CONFIG, $key));

        $lang_file_path = $this->config->item("languages_directory").$lang_item['filename'];

        if(file_exists($lang_file_path)){
            include($lang_file_path);
            if(!isset($lang))
                die(sprintf(Language::ERROR_NO_LANG_IN_FILE, $lang_file_path));
            return $lang;
        }else
            die(sprintf(Language::ERROR_NO_FILE, $key, $lang_file_path));
    }


    /**
     * @return false | string
     */
    private function getSelectedLanguage(){
        return isset($_COOKIE['lang']) ? $_COOKIE['lang'] : false;
    }

    private function getLanguageFromHeaders(){
        // TODO: Make this :)
    }

    private function setSelectedLanguage(){

    }

    private function suffixDatabaseField($fieldName){

    }

    private function getDefaultLanguageKey(){
        return array_keys($this->languages)[0];
    }

    /**
     * @param string $key
     * @return bool
     */
    private function isValidLanguageKey($key){
        return isset($this->languages[$key]);
    }

    /**
     * @param string $key
     * @return string
     */
    public function item($key){
        if(!isset($this->currentLang) || !isset($this->defLang))
            die(Language::ERROR_LANGUAGES_NOT_LOADED);
        if(isset($this->currentLang[$key])) return $this->currentLang[$key];
        if(isset($this->defLang[$key])) return $this->defLang[$key];
        die(sprintf(Language::ERROR_NO_VALUE, $key, debug_backtrace()[0]['file'], debug_backtrace()[0]['line']));
    }
}