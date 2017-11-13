<?php

defined('ACCESS') || Error::exitApp();

/**
*
*/
class Staff extends FuniObject
{
	public $staffId;
	public $name;
	public $deptId;
	public $dept;
	public $isUser = false;
	

	function __construct($staffId)
	{
		# code...
		global $registry;
		$data = $registry->get('db')->fetchStaffDetails($staffId);
		if($data === false || is_null($data)){
			$this->id = null;
			$this->name = '';
			$this->username = '';
			$this->deptId = '';
			$this->dept = '';
		}else{
			$this->id = $staffId;
			$this->name = $data->name;
			$this->deptId = $data->deptId;
			$this->dept = $this->getDept();
		}
	}

	public function getDept()
	{
		# code...
		global $registry;
		return $registry->get('db')->getUserRole($this->deptId);
	}



	#static functions

	public static function addNew(Array $data)
	{
		# code...
		global $registry;
		return $registry->get('db')->addNewStaff($data);
	}

	public static function fetchAll()
	{
		# code...
		global $registry;
		return $registry->get('db')->fetchAllStaff();
	}
	




#end of class
}
