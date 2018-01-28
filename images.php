<?php

class images extends entity{
	protected static $table_fields = array('title','description','filename','type','size');
	protected static $table = "images";
	protected static $key = "photo_id";

	public $id;
	public $title;
	public $description;
	public $filename;
	public $type;
	public $size;

	public $tmp_path;
	public $upload_directory = "images";
	public $errors = array();
	public $upload_errors_array = array(

		UPLOAD_ERR_OK => "There is no error",
		UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive.",
		UPLOAD_ERR_FORM_SIZE => "The uploaded file exceed the MAX_FILE_SIZE directive.",
		UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded.",
		UPLOAD_ERR_NO_FILE => "No file was uploaded.",
		UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder.",
		UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk.",
		UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload."
	);

	public function set_file($file){
		if(empty($file) || !$file || !is_array($file)){
			$this->errors[] = "There was no file uploaded here.";
			return false;
		}else if($file['error'] !=0){
			$this->errors[] = $this->upload_errors_array[$file['error']];
			return false;
		}else{
			$this->filename = basename($file['name']); 
			$this->tmp_path = $file['tmp_name'];
			$this->type = $file['type'];
			$this->size = $file['size'];
		}
	}

	public function save(){
		if($this->id){
			$this->update();
		}else{
			if(!empty($this->errors)){
				return false;
			}
			if(empty($this->filename) || empty($this->tmp_path)){
				$this->errors[] = "The file was not available.";
				return false;
			}

			$target_path = SITE_ROOT . DS . $this->upload_directory . DS . $this->filename;

			if(file_exists($target_path)){
				$this->errors[] = "The file {$this->filename} already exists.";
			}
			if(move_uploaded_file($this->tmp_path, $target_path)){
				if($this->create()){
					unset($this->tmp_path);
					return true;
				}
			}else{
				$this->errors[] = "The file directory probably does not have permission!";
				return false;
			}
		}
	}

}
