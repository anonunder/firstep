<?php

abstract class entity{

	public static function find_all(){
		$users = static::find_query("SELECT * FROM ".static::$table."");
		$users->setFetchMode(PDO::FETCH_CLASS, static::class);
		$found_user = $users->fetchAll();
		return $found_user;
	}

	public static function find_by_id($user_id){
		$result_set = static::find_query("SELECT * FROM ".static::$table." where ".static::$key." = {$user_id} LIMIT 1");
		$found_user = $result_set->fetchObject(static::class);
		return $found_user;
	}

	public static function find_query($sql){
		$result_set = db::query($sql);
		return $result_set;
	}

	protected function properties(){
		$properties = array();
		foreach (static::$table_fields as $db_field){
			if(property_exists($this, $db_field)){
				$properties[$db_field] = $this->$db_field;
			}
		}
		return $properties;
	}
	public function create(){
		$db = db::getConnection()->conn;
		$properties = $this->properties();
		$table_key = implode(",",array_keys($properties));
		$table_value = implode(",:",array_keys($properties));
		$sql = "INSERT INTO ".static::$table." (" . $table_key . ") ";
		$sql.= "VALUES (:" . $table_value . ")";
		$stmt = $db->prepare($sql);
		foreach($properties as $key=>$value){
			$stmt->bindParam(":".$key,$this->$key);
		}
		if($stmt->execute()){
			$this->id = db::the_insert_id();
			return true;
		}else{
			return false;
		}
		return $stmt;
	}

	public function delete(){
		$db = db::getConnection()->conn;
		$sql = "DELETE FROM ".self::$table." WHERE ";
		$sql .= "".static::$key." = :id ";
		$sql .= "LIMIT 1";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
		if($stmt->execute()){
			return true;
		}else{
			return false;
		}
	}

	public function update(){
		$db = db::getConnection()->conn;
		$properties = $this->properties();
		$table_key = implode(",",array_keys($properties));
		$table_value = implode(",:",array_keys($properties));
		$properties_pairs = array();
		$new = array_filter($properties);
		foreach($new as $key => $value){
			$properties_pairs[] = "{$key}=:$key";
		}

		$sql = "UPDATE " .static::$table." SET ";
		$sql .= implode(", ",$properties_pairs);
		$sql .= " where ".static::$key." = :id";
		$stmt = $db->prepare($sql);
		foreach($new as $key=>$value){
			$stmt->bindParam(":".$key,$this->$key);
		}
		$stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
		if($stmt->execute()){
			return true;
		}else{
			return false;
		}
	}
}
