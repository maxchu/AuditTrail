<?php 

class Database {
	
	private $connection;
	private static $instance;
	private $host = 'host';
	private $username = 'username';
	private $password = 'password';
	private $database = 'database';

	public static function getInstance() {
		if(!self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		try
		{
			$dsn = 'mysql:dbname=' . $this->database . ';host=' . $this->host;
			$this->connection = new PDO($dsn, $this->username, $this->password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		}
		catch (PDOException $e)
		{
			die($e->getMessage());
		}
	
	}
	
	private function __clone() { }

	public function getConnection() {
		return $this->connection;
	}
	
	public function beginTransaction() {
		$this->connection->beginTransaction();
		return true;
	}
	
	public function rollBack() {
		$this->connection->rollBack();
		return true;
	}
	
	public function commit() {
		$this->connection->commit();
		return true;
	}
	
	public function lastInsertId() {
		return $this->connection->lastInsertId();
	}
	
	public function create ($table, $params){
		if (empty($table) || empty($params)) {
			return false;
		}
		
		$fields = array();
		foreach ($params as $key => $value) {
			$fields[] = ltrim($key, ':') . '=' . $key;
		}
		$sql = "INSERT INTO $table SET " . implode(', ', $fields);
		$query = $this->connection->prepare($sql);
		$query->execute($params);
		
		return true;
	}
	
	public function update ($table, $params, $primaryKey){
		if (empty($table) || empty($params)) {
			return false;
		}
		
		$fields = array();
		foreach ($params as $key => $value) {
			$fields[] = ltrim($key, ':') . '=' . $key;
		}
		$sql = "UPDATE $table SET " . implode(', ', $fields) . " WHERE $primaryKey=:$primaryKey";
		$query = $this->connection->prepare($sql);
		$query->execute($params);
	
		return true;
	}

}
