<?php
//use application\libs\RoomDatabase as Db;

defined('ACCESS') || Error::exitApp();

/**
*
*/
class Menu extends FuniObject
{
	public $id;
	public $typeId;
	public $type;
	public $name;
	public $price;
	public $reductions;

	private $_db = null;


	function __construct($menuId)
	{
		# code...
		global $registry;
		$this->_db = $registry->get('menuDb');

		$data = $this->_db->fetchDetails($menuId);

		$this->id = $data->id;
		$this->typeId = $data->typeId;
		$this->type = $this->_getType();
		$this->name = $data->name;
		$this->price = $data->price;
		$this->reductions = $data->reductions;

	}

	private function _getType()
	{
		# code...
		global $registry;
		return $this->_db->fetchType($this->typeId);
	}



	/****************************
		STATIC FUNCTIONS
		*************************/

	public static function fetchAll()
	{
		# code...
		global $registry;

		$db = $registry->get('menuDb');
		return $db->fetchAll();
	}

	public static function fetchTypes()
	{
		# code...
		global $registry;

		$db = $registry->get('menuDb');
		return $db->fetchTypes();
	}

	public static function addNew(Array $data)
	{
		# code...
		global $registry;

		$db = $registry->get('menuDb');
		return $db->addNew($data);
	}


	public static function checkIfAlreadyExist($menuName)
	{
		# code...
		global $registry;
		return $registry->get('menuDb')->checkIfAlreadyExist($menuName);
	}

	public static function updateDetail($desc, $value, $menuId)
	{
		# code...
		global $registry;
		return $registry->get('menuDb')->updateDetail($desc, $value, $menuId);
	}


	# End of Class
}
