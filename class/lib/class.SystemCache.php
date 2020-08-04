<?php

class SystemCache{
	private static $instance;
	private static $name;
	private $obj_array=array();
	
	public static function getInstance(){
		 if (!SystemCache::$instance instanceof self) {
             SystemCache::$instance = new SystemCache();
        }
        return SystemCache::$instance;
	}
	public static function GI(){
		 if (!SystemCache::$instance instanceof self) {
             SystemCache::$instance = new SystemCache();
        }
        return SystemCache::$instance;
	}	
	public function doPutCache($name,$object){ 
		$this->obj_array[$name]=$object;
	}
	public  function doCacheName($nm){ 
		$this->name=$nm;	
	}	
	public function doSave(){
		$this->write(json_encode($this->obj_array));
	}
	public function write($data){
		if (count($this->obj_array)>0){ 
			$fp=fopen("sys_cache/".$this->name.".json","w+");
			fwrite($fp, $data);
			fclose($fp);   
		}
	}
	public function getCache(){
		if (file_exists("sys_cache/".$this->name.".json")){
			$data=file_get_contents("sys_cache/".$this->name.".json");
			return json_decode($data); 
		}
		return array();
	}	

}



?>