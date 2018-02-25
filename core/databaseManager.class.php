<?php if(!defined("BASE_PATH")) die("Direct file access is forbidden.");


/**
 * Class DB
 *
 * @property PDO db
 */
class DB {
    private $config;

    /**
     * DB constructor.
     * @param Config $config
     */
    public function __construct($config){
	    $this->config = $config;
        $dbopts = $this->config->item("dbopts");
		try
		{
			$this->db = new PDO('mysql:host='.$dbopts['db_host'].';port='.$dbopts['db_port'].';dbname='.$dbopts['db_name'], $dbopts['db_user'], $dbopts['db_pass']);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->db->exec("set names utf8");
		}
		catch (PDOException $e) 
		{
			$this->db = null;
			die($e->getMessage());
		}
	}

	public function insertWord($browser, $title,$description,$content,$site_id){
		$query = $this->db->prepare("INSERT INTO app_analysis_wordlog (browser, title,description,content,site_id) VALUES (:browser,:title,:description,:content,:site_id)");
		$query->bindValue(':browser',$browser);
		$query->bindValue(':title',$title);
		$query->bindValue(':description',$description);
		$query->bindValue(':content',$content);
		$query->bindValue(':site_id',$site_id);
		return $query->execute();
	}
	public function selectQueues(){
		$statement = $this->db->prepare("SELECT * FROM app_analysis_wordqueue");
		$statement->execute();
		return $statement->fetchAll();
	}

    public function insertUser($obj_array){
        $sql = "INSERT INTO `users` (`username`, `password`, `user_type`, `student_grade`) VALUES (?, ?, ?, ?)";
        $query = $this->pdo->prepare($sql);

        $query->execute(array(
            $obj_array['username'],
            $obj_array['password'],
            $obj_array['user_type'],
            $obj_array['student_grade']
        ));

        return $this->pdo->lastInsertId();
    }

    public function getUserByUsername($username) {
        $sql = "SELECT * FROM `users` WHERE `username`=?";

        $query = $this->pdo->prepare($sql);
        $query->execute(array($username));
        $result_array = $query->fetch(PDO::FETCH_ASSOC);

        if(count($result_array) > 0){
            return $result_array;
        }
        return false;
    }

    public function getUserById($user_id) {
        $sql = "SELECT * FROM `users` WHERE `user_id`=?";

        $query = $this->pdo->prepare($sql);
        $query->execute(array($user_id));
        $result_array = $query->fetch(PDO::FETCH_ASSOC);

        if(count($result_array) > 0){
            return $result_array;
        }
        return false;
    }

    public function insertLesson($obj_array){
        $sql = "INSERT INTO `lessons` (`l_stud_id`, `l_teach_id`, `l_pic_path`, `l_file_path`, `s_id`, `l_body`, `l_target_class`, `l_title`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $query = $this->pdo->prepare($sql);

        $query->execute(array(
            $obj_array['l_stud_id'],
            $obj_array['l_teach_id'],
            $obj_array['l_pic_path'],
            $obj_array['l_file_path'],
            $obj_array['s_id'],
            $obj_array['l_body'],
            $obj_array['l_target_class'],
            $obj_array['l_title']
        ));

        return $this->pdo->lastInsertId();
    }

    public function approveLesson($l_id){
        $sql = "UPDATE `lessons` SET `l_status`=1 WHERE `l_id`=?";
        $query = $this->pdo->prepare($sql);

        $query->execute(array($l_id));
        return ($query->rowCount() > 0);
    }

    public function rejectLesson($l_id, $l_return_reason){
        $sql = "UPDATE `lessons` SET `l_status`=2, `l_body`=? WHERE `l_id`=?";
        $query = $this->pdo->prepare($sql);

        $query->execute(array(
            $reason,
            $l_id
        ));
        return ($query->rowCount() > 0);
    }

    public function updateLesson($l_id, $obj_array){
        $params = array();
        $sql = "UPDATE `lessons` SET ";
        $i = 1;
        $arr_count = count($obj_array);
        foreach ($obj_array as $key => $value) {
            $sql .= "`" . $key . "`=?";
            $params[] = $value;
            $sql .= ($i < $arr_count ? ", " : " ");
            $i++;
        }
        $sql .= "WHERE `l_id`=?";
        $params[] = $l_id;

        $query = $this->pdo->prepare($sql);
        $query->execute($params);

        return ($query->rowCount() > 0 ? true : false);
    }

    public function incrementLessonVisits($l_id) {
        $sql = "UPDATE `lessons` SET `l_views_count`=l_views_count+1 WHERE `l_id`=?";
        $query = $this->pdo->prepare($sql);

        $query->execute(array(
            $l_id
        ));
        return ($query->rowCount() > 0);
    }

    public function getAllLessonsCount($approved=1) {
        $params = array();
        $sql = "SELECT COUNT(*) AS count FROM `lessons` WHERE `l_status`=?";

        $query = $this->pdo->prepare($sql);
        $query->execute(array($approved));
        $result_array = $query->fetch(PDO::FETCH_ASSOC);

        if(count($result_array) > 0){
            return (int) $result_array['count'];
        }
        return 0;
    }

    public function getLessons($order_by=false, $limit_1=false, $limit_2=false, $approved=0, $search_options=false){
        $args = array($approved);

        $sql  = "SELECT SQL_CALC_FOUND_ROWS `lessons`.*, `subjects`.*, teacher.`fullname` AS t_fullname, student.`fullname` AS s_fullname ";
        $sql .= "FROM `lessons` ";
        $sql .= "JOIN `subjects` ON `lessons`.`s_id`=`subjects`.`s_id` ";
        $sql .= "JOIN `users` teacher ON `lessons`.`l_teach_id` = teacher.`user_id` ";
        $sql .= "JOIN `users` student ON `lessons`.`l_stud_id` = student.`user_id` ";
        $sql .= "WHERE `l_status`=? ";

        if(is_array($search_options)){
            if(!empty($search_options['teach'])){
                $sql .= "AND teacher.`user_id` = ? ";
                $args[] = $search_options['teach'];
            }
        }

        if(is_array($search_options)){
            if(!empty($search_options['subj'])){
                $sql .= "AND `lessons`.`s_id` = ? ";
                $args[] = $search_options['subj'];
            }
        }

        if(is_array($search_options)){
            if(!empty($search_options['class'])){
                $sql .= "AND `l_target_class` = ? ";
                $args[] = $search_options['class'];
            }
        }

        if($order_by){
            $sql .= "ORDER BY ".$order_by." ";
        }

        if(is_int($limit_1) && is_int($limit_2)){
            $sql .= " LIMIT ".$limit_1.", ".$limit_2;
        }

        $query = $this->pdo->prepare($sql);
        $query->execute($args);
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        if(count($result_array) > 0){
            return $result_array;
        }

        return false;
    }

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

    public function pendingApprovalCount($teacher_id){
        $sql = "SELECT COUNT(l_id) as count FROM `lessons` WHERE `l_teach_id`=? AND `l_status`=0";

        $query = $this->pdo->prepare($sql);
        $query->execute(array($teacher_id));
        $result_array = $query->fetch(PDO::FETCH_ASSOC);

        if(count($result_array) > 0){
            return (int) $result_array['count'];
        }
        return 0;
    }

    public function returnedCount($student_id){
        $sql = "SELECT COUNT(l_id) as count FROM `lessons` WHERE `l_stud_id`=? AND `l_status`=2";

        $query = $this->pdo->prepare($sql);
        $query->execute(array($student_id));
        $result_array = $query->fetch(PDO::FETCH_ASSOC);

        if(count($result_array) > 0){
            return (int) $result_array['count'];
        }
        return 0;
    }

    public function getAllTeachers() {
        $sql = "SELECT * FROM `users` WHERE `user_type`=1";

        $query = $this->pdo->prepare($sql);
        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        if(count($result_array) > 0){
            return $result_array;
        }
        return false;
    }

    public function getAllSubjects() {
        $sql = "SELECT * FROM `subjects`";

        $query = $this->pdo->prepare($sql);
        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        if(count($result_array) > 0){
            return $result_array;
        }
        return false;
    }

    public function getLesson($lesson_id) {
        $sql  = "SELECT lessons.*, subjects.*, teacher.`fullname` AS t_fullname, student.`fullname` AS s_fullname, student.`user_id` AS student_id ";
        $sql .= "FROM `lessons` ";
        $sql .= "JOIN `subjects` ON `lessons`.`s_id`=`subjects`.`s_id` ";
        $sql .= "JOIN `users` teacher ON `lessons`.`l_teach_id` = teacher.`user_id` ";
        $sql .= "JOIN `users` student ON `lessons`.`l_stud_id` = student.`user_id` ";
        $sql .= "WHERE `l_id`=?";

        $query = $this->pdo->prepare($sql);
        $query->execute(array($lesson_id));
        $result_array = $query->fetch(PDO::FETCH_ASSOC);

        if(count($result_array) > 0){
            return $result_array;
        }
        return false;
    }

    public function getPendingApproval($teacher_id) {
        $sql  = "SELECT lessons.*, subjects.*, teacher.`fullname` AS t_fullname, student.`fullname` AS s_fullname ";
        $sql .= "FROM `lessons` ";
        $sql .= "JOIN `subjects` ON `lessons`.`s_id`=`subjects`.`s_id` ";
        $sql .= "JOIN `users` teacher ON `lessons`.`l_teach_id` = teacher.`user_id` ";
        $sql .= "JOIN `users` student ON `lessons`.`l_stud_id` = student.`user_id` ";
        $sql .= "WHERE teacher.`user_id`=? AND `l_status`=0";

        $query = $this->pdo->prepare($sql);
        $query->execute(array($teacher_id));
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        if(count($result_array) > 0){
            return $result_array;
        }
        return false;
    }

    public function getRejected($student_id) {
        $sql  = "SELECT lessons.*, subjects.*, teacher.`fullname` AS t_fullname, student.`fullname` AS s_fullname ";
        $sql .= "FROM `lessons` ";
        $sql .= "JOIN `subjects` ON `lessons`.`s_id`=`subjects`.`s_id` ";
        $sql .= "JOIN `users` teacher ON `lessons`.`l_teach_id` = teacher.`user_id` ";
        $sql .= "JOIN `users` student ON `lessons`.`l_stud_id` = student.`user_id` ";
        $sql .= "WHERE student.`user_id`=? AND `l_status`=2";

        $query = $this->pdo->prepare($sql);
        $query->execute(array($student_id));
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        if(count($result_array) > 0){
            return $result_array;
        }
        return false;
    }

    public function userHasPermissionToApprove($user_id, $l_id){
        $sql = "SELECT * FROM `lessons` WHERE `l_id`=? AND `l_teach_id`=?";
        $query = $this->pdo->prepare($sql);
        $query->execute(array($l_id, $user_id));
        $result_array = $query->fetch(PDO::FETCH_ASSOC);
        return count($result_array) > 0 ? true : false;
    }

    public function updateUserPassword($user_id, $new_password){
        $sql = "UPDATE `users` SET `password`=? WHERE `user_id`=?";
        $query = $this->pdo->prepare($sql);
        $query->execute(array($new_password, $user_id));

        if (  $query->rowCount() == 0  ) {
            return false;
        }else{
            return true;
        }


    }
}