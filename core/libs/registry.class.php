<?php
/*
* Register Classes that require a single instance
*
*/

defined('ACCESS') or Error::exitApp();

class Registry extends FuniObject{

private $registry = array();
private static $instance;

	private function __construct(){
		
	}

	public function set($key, $value, $prefix = false){
	if(is_object($value)){
	 if(!isset($this->registry[$key])){
		 $this->registry[$key] = $value; 
		 return;
	 }
	 Error::throwException('Class ( ' . $key . ' ) Already Registered');
	}
	}
	
	public function get($key, $prefix = false){
	 if(!isset($this->registry[$key])){
		 throw new Exception('Class not Registered...');
	 }	
	 return $this->registry[$key];
	}
	
	
	
	/*public static function getInstance(){
		if(self::$instance === NULL){
		   self::$instance = new Registry();	
		}
		return self::$instance;
	}*/
	
	public static function getInstance(){
	 if(!(self::$instance instanceof self)){
		self::$instance = new self; 
	 }
	 return self::$instance;
	}
}