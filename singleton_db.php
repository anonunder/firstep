<?php
class db{
	private static $instance;
	public $conn;
	private function __construct(){
		$this->conn = new PDO("mysql:host=localhost;dbname=name", "user_name", "pass_word");
	}
	public static function getConnection(){
		if(!self::$instance){
			self::$instance = new db;
		}
		return self::$instance;
	}

	public static function query($sql){
		$result = self::getConnection()->conn->query($sql);
		return $result;
	}

	public static function the_insert_id(){
		return self::getConnection()->conn->lastInsertId();
	}
}


