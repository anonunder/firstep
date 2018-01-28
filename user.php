<?php

class user extends entity{
	protected static $table_fields = array('username','password');
	protected static $table = "users";
	protected static $key = "id";
	public $id;
	public $username;
	public $password;

	public static function verify_user($username,$password){
		$db = db::getConnection()->conn;
		$sql = "SELECT * FROM ".self::$table." WHERE username = :username AND password = :password";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':username',$username);
		$stmt->bindParam(':password',$password);
		$stmt->execute();
		$user = $stmt->fetch(PDO::FETCH_OBJ);
		return $user;

	}
	
	

}
