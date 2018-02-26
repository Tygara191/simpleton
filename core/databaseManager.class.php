<?php if(!defined("BASE_PATH")) die("Direct file access is forbidden.");


/**
 * Class DB
 *
 * @property PDO $pdo
 */
class DatabaseManager {
    private $config;

    /**
     * DB constructor.
     * @param Config $config
     */
    public function __construct($config){
	    $this->config = $config;
        $dbopts = $this->config->item("dbopts");
		try {
			$this->pdo = new PDO('mysql:host='.$dbopts['db_host'].';port='.$dbopts['db_port'].';dbname='.$dbopts['db_name'], $dbopts['db_user'], $dbopts['db_pass']);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->pdo->exec("set names utf8");
		} catch (PDOException $e) {
			$this->pdo = null;
			die($e->getMessage());
		}
	}

	// ------------------ Generic stuff ---------------------------

    /**
     * @return int
     */
    public function foundRows() {
        $sql = "SELECT FOUND_ROWS() as count";
        $query = $this->pdo->prepare($sql);
        $query->execute();
        $result_array = $query->fetch(PDO::FETCH_ASSOC);

        if(count($result_array) > 0){
            return (int) $result_array['count'];
        }
        return 0;
    }

    /**
     * @param int $id
     * @param array $object
     * @return bool
     */
    private function update($id, $object){
        $params = array();
        $sql = "UPDATE `lessons` SET ";
        $i = 1;
        $arr_count = count($object);
        foreach ($object as $key => $value) {
            $sql .= "`" . $key . "`=?";
            $params[] = $value;
            $sql .= ($i < $arr_count ? ", " : " ");
            $i++;
        }
        $sql .= "WHERE `l_id`=?";

        $params[] = $id;
        $query = $this->pdo->prepare($sql);
        $query->execute($params);

        return ($query->rowCount() > 0 ? true : false);
    }

    // ------------------ Authentication --------------------------
    /**
     * @param array $object
     * @return string
     */
    public function insertUser($object){
        $sql = "INSERT INTO `users` (`username`, `password`, `user_type`, `student_grade`) VALUES (:username, :password, :user_type, :student_grade)";
        $query = $this->pdo->prepare($sql);

        $query->bindValue('username', $object['username']);
        $query->bindValue('password', $object['password']);
        $query->bindValue('user_type', $object['user_type']);
        $query->bindValue('student_grade', $object['student_grade']);

        $query->execute();

        return $this->pdo->lastInsertId();
    }

    /**
     * @param $user_id
     * @return false|array
     */
    public function getUserById($user_id) {
        $sql = "SELECT * FROM `users` WHERE `user_id`=:user_id";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('user_id', $user_id);
        $query->execute();
        $result_array = $query->fetch(PDO::FETCH_ASSOC);

        if(count($result_array) > 0){
            return $result_array;
        }
        return false;
    }

    /**
     * @param $username
     * @return false|array
     */
    public function getUserByUsername($username) {
        $sql = "SELECT * FROM `users` WHERE `username`=:username";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('username', $username);
        $query->execute();

        $result_array = $query->fetch(PDO::FETCH_ASSOC);
        return count($result_array) > 0 ? $result_array : false;
    }

    /**
     * @param int $user_id
     * @param string $new_password
     * @return bool
     */
    public function updateUserPassword($user_id, $new_password){
        $sql = "UPDATE `users` SET `password`=:password WHERE `user_id`=:user_id";
        $query = $this->pdo->prepare($sql);
        $query->bindValue('password', $new_password);
        $query->bindValue('user_id', $user_id);
        $query->execute();
        return $query->rowCount() > 0;
    }
}