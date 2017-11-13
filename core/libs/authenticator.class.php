<?php
/*
* To use this class...your php version must be
* v 5.5 or higher
* admin : iamlegend@1
*/
use application\libs\Database;

defined('ACCESS') || Error::exitApp();

/**
* 
*/
class Authenticator extends FuniObject
{
	private $_db;

	function __construct(Database $db)
	{
		# code...
		$this->_db = $db; 
	}

	public function hashPassword($pwd='')
	{
		# use default crytpt function & cost
		return password_hash($pwd, PASSWORD_DEFAULT);
	}

	public function verifyPassword($username, $pwd)
	{
		# code...

		$data = (object) $this->_db->getPwdHash($username);
		//var_dump($data);die;
		
		# if pwd cud not be retrived for this username
		if($data->hash == ''){ return false; }

		if(password_verify($pwd, trim($data->hash))){
			
			if(password_needs_rehash($pwd, PASSWORD_DEFAULT)){
				$newHash = password_hash($pwd, PASSWORD_DEFAULT);
				$this->_db->updatePwdHash($data->id, $newHash);
			}
			return $data->staffId;
		}
		return false;
				
	}

	public function verifyPassword2($pwd, $resetHash)
	{
		# code...
		if(password_verify($this->hashPassword($pwd), $resetHash)){
			return true;
		}else{
			return false;
		}
	}

	public function checkPrivilege($usertype, Array $allowedUserTypes, $redirect = false)
	{
		global $registry;
		# code...
		if(array_search($usertype, $allowedUserTypes) === false){
			if($redirect){
				header('Location: ' . $registry->get('config')->get('baseUri') . '/dashboard');
				exit;
			}else{ return false; }
		}else{ return true; }
	}
}