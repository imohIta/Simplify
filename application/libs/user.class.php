<?php

defined('ACCESS') || Error::exitApp();

/**
* Had to edit this class froma normal class to a decorator object that collects a staff class
*/
class User extends FuniObject
{
	public $id;
	public $staffId;
	public $name;
	public $username;
	public $privilege;
	protected $_activeAcct;
	public $role;
	public $tbl;
	public $isUser = true;

	public $autoId;

	function __construct(Staff $staff)
	{
		# code...
		global $registry;
		//$data = $registry->get('db')->fetchUserDetails($userId);
		//if($data === false || is_null($data)){
		if(is_null($staff->id)){
			$this->id = null;
			$this->staffId = null;
			$this->name = '';
			$this->username = '';
			$this->privilege = '';
			$this->_activeAcct  = '';
			$this->role = '';
		}else{
			$data = $registry->get('db')->fetchUserDetails2($staff->id);

			$this->staffId = $staff->id;

			/** had to change id from the original id in the users table to staffId to help with backward compatibility
			the id in the users table has been assigned to autoId

			**/
			$this->id = $staff->id;
			$this->name = $staff->name;
			$this->username = $data->username;
			$this->privilege = $staff->deptId;
			$this->_activeAcct  = $staff->deptId;
			$this->role = $staff->dept;
			$this->autoId = $data->id;
		}

		# resturant uses kitchen items in stock
		//$this->$tbl = ($this->_activeAcct == 10) ? 'kitchenStk' : strtolower(str_replace(' ', '_', $this->role)) . 'Stk';
		if($this->_activeAcct == 13){
			$this->tbl = 'store';
		}else{
			$this->tbl = strtolower(str_replace(' ', '_', $this->role)) . 'Stk';
		}
	}

	private function _getRole()
	{
		# code...
		global $registry;
		return $registry->get('db')->getUserRole($this->_activeAcct);
	}

	public function getOriginalRole(){
		global $registry;
		return $registry->get('db')->getUserRole($this->privilege);
	}

    #overides funiobject Set
	public function set($key, $value, $prefix = true){
	   $key = ($prefix) ? '_'.trim($key,'_') : trim($key,'_');
	   if(property_exists(__CLASS__, $key)){
			$this->{$key} = $value;
			#if privilege is reset...reset role also
			if($key == '_activeAcct'){
				$this->role = $this->_getRole($this->_activeAcct);
				# resturant uses kitchen items in stock
				//$this->$tbl = ($this->_activeAcct == 10) ? 'kitchenStk' : strtolower(str_replace(' ', '_', $this->role)) . 'Stk';
				if($this->_activeAcct == 13){
					$this->tbl = 'store';
				}else{
					$this->tbl = strtolower(str_replace(' ', '_', $this->role)) . 'Stk';
				}
			}
		}
	}

	public function fetchNotifications($limit='')
	{
		# code...
		global $registry;
		return $registry->get('db')->fetchUserNotifications($this->get('activeAcct'), $limit);
	}

	public function countUnreadNotifications()
	{
		# code...
		global $registry;
		return $registry->get('db')->countUnreadNotifications($this->get('activeAcct'));
	}

   /* Static Functions */

  public static function getTblByPrivilege($priv){
	global $registry;

	switch ($priv) {
		case 8:
			return 'pool_barStk';
			break;
		case 9:
			return 'main_barStk';
			break;
		case 10:
			return 'resturantStk';
			break;
		case 11:
			return 'resturant_drinksStk';
			break;
		case 12:
			return 'kitchenStk';
			break;
		case 13:
			return 'store';
			break;
		case 15:
			return 'house_keepingStk';
			break;

		default:
			return '';
			break;
	}
  }


  public static function getRole($priv){
	global $registry;
	return $registry->get('db')->getUserRole($priv);

  }

  public static function fetchAll($includeMgt = true)
  {
  	# code...
  	global $registry;
  	return $registry->get('db')->fetchAllUsers($includeMgt);
  }


  public static function fetchAllPrivileges($includeMgt = false)
  {
  	# code...
  	global $registry;
  	return $registry->get('db')->fetchAllPrivileges($includeMgt);
  }

  public static function addNew(Array $data)
  {
  	# code...
  	global $registry;
  	return $registry->get('db')->addNewUserAcct($data);
  }

  public static function delete($staffId)
  {
  	# code...
  	global $registry;
  	return $registry->get('db')->deleteUser($staffId);
  }

  public static function update($desc, $value, $userId)
  {
  	# code...
	global $registry;
	return $registry->get('db')->updateUserDetails($desc, $value, $userId);
  }

  public static function fetchDetails($userId)
  {
  	# code...
  	global $registry;
  	return $registry->get('db')->fetchUserDetails($userId);
  }





#end of class
}
