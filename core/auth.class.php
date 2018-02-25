<?php if (!defined('BASE_PATH')) exit('No direct script access allowed');


/**
 * Class Auth
 * @property Encryption $encryption
 * @property Config $config
 * @property array $user
 * @property DatabaseManager $db
 */
class Auth{

    private $encryption;
    private $config;
    private $user;
    private $db;

    /**
     * Auth constructor.
     * @param Config $config
     * @param DatabaseManager $db
     * @param Encryption $encryption
     */
    function __construct($config, $db, $encryption){
        $this->encryption = $encryption;
        $this->config = $config;
        $this->db = $db;
    }

    /**
     * @param string $password
     * @return string
     */
    private function hashPassword($password) {
        // TODO: Add proper encryption and encoding here :)
        return md5($password);
    }

    /**
     * @param int $user_id
     * @param string $username
     * @return bool|string
     */
    private function compileSessionString($user_id, $username){
        $string  = (string) $user_id;
        $string .= ".".$username;
        $string .= ".".$this->config['session_salt'];
        return $this->encryption->encode($string);
    }

    /**
     * @param string $string
     * @return false|int
     */
    private function decompileSessionString($string) {
        $string = $this->encryption->decode($string);
        $components = explode(".", $string);
        if($components[2] == $this->config['session_salt']){
            return (int) $components[0];
        }
        return false;
    }

    /**
     * @return bool
     */
    private function setSession() {
        $string = $this->compileSessionString($this->user['user_id'], $this->user['username']);
        $_SESSION['auth_identifier'] = $string;
        $_SESSION['logged_site'] = true;
        return true;
    }

    /**
     * @return false|int
     */
    private function getSessionUserId() {
        if(!empty($_SESSION['auth_identifier']) && !empty($_SESSION['logged_site'])){
            $auth_identifier = $_SESSION['auth_identifier'];
            $logged_site = $_SESSION['logged_site'];

            if($logged_site == true){
                return $this->decompileSessionString($auth_identifier);
            }
        }
        return false;
    }

    /**
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function doLogin($username, $password){
        if($this->isUserValid($username, $password)){
            return $this->setSession();
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isUserLogged(){
        $user_id = $this->getSessionUserId();
        if(is_int($user_id)){

            $user = $this->db->getUserById($user_id);

            if(is_array($user)){
                $this->user = $user;
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $username
     * @param string $password
     * @return bool
     */
    private function isUserValid($username, $password) {
        $user = $this->db->getUserByUsername($username);
        $pass_hash = $this->hashPassword($password);

        if(is_array($user)){
            if($user['password'] == $pass_hash){
                $this->user = $user;
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function logout() {
        session_destroy();
        return true;
    }

    /**
     * @param string $old_password
     * @param string $new_password
     * @return bool
     */
    public function changePassword($old_password, $new_password){
        if ($this->isUserLogged()) {
            $old_pass_hash = $this->hashPassword($old_password);
            if ($this->user['password'] == $old_pass_hash) {
                $new_pass_hash = $this->hashPassword($new_password);

                if ($this->db->updateUserPassword($this->user['user_id'], $new_pass_hash)) {
                    return true;
                } else {
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}