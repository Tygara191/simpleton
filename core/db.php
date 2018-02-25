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
}