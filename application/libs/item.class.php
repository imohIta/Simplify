<?php
//use application\libs\RoomDatabase as Db;

defined('ACCESS') || Error::exitApp();

/**
*
*/
class Item extends FuniObject
{
	public $id;
	public $typeId;
	public $type;
	public $name;
	public $unitId;
	public $unit;

	private $_db = null;

	function __construct($itemId)
	{
		# code...
		global $registry;
		$this->_db = $registry->get('itemDb');

		$data = $this->_db->fetchDetails($itemId);
		if(is_null($data) || empty($data)){

			# if Item not found

			$this->id = null;
			$this->typeId = null;
			$this->type = null;
			$this->name = null;
			$this->unitId = null;
			$this->unit = null;
		}else{
			$this->id = $data->id;
			$this->typeId = $data->typeId;
			$this->type = $this->_getType();
			$this->name = $data->name;
			$this->unitId = $data->unitId;
			$this->unit = $this->_getUnit();
		}

	}

	private function _getType()
	{
		# code...
		global $registry;
		return $this->_db->fetchType($this->typeId);
	}

	private function _getUnit()
	{
		# code...
		global $registry;
		return $this->_db->fetchUnit($this->unitId);
	}





	/****************************
		STATIC FUNCTIONS
		*************************/

	public static function deleteStockItem($itemId){
		global $registry;
		$db = $registry->get('itemDb');
		return $db->deleteStockItem($itemId);
	}

	public static function deleteMenuItem($itemId){
		global $registry;
		$db = $registry->get('itemDb');
		return $db->deleteMenuItem($itemId);
	}

	public static function fetchTypes()
	{
		# code...
		global $registry;
		$db = $registry->get('itemDb');
		return $db->fetchTypes();
	}

	public static function fetchUnits()
	{
		# code...
		global $registry;
		$db = $registry->get('itemDb');
		return $db->fetchUnits();
	}

	public static function fetchAll($tbl = '', $excludeFinished = false)
	{
		# code...

		global $registry;
		$thisUser = unserialize($registry->get('session')->read('thisUser'));

		$tbl = ($tbl) ? $tbl : $thisUser->tbl; 

		$db = $registry->get('itemDb');
		return $db->fetchAll($tbl, $excludeFinished);
	}

	public static function fetchReduced($threshold)
	{
		# code...
		global $registry;
		$thisUser = unserialize($registry->get('session')->read('thisUser'));

		$tbl = $thisUser->tbl;

		$db = $registry->get('itemDb');
		return $db->fetchReduced($tbl, $threshold);
	}

	public static function addNew(Array $data)
	{
		# code...
		global $registry;
		return $registry->get('itemDb')->addNew($data);
	}

	public static function checkIfAlreadyExist($itemName)
	{
		# code...
		global $registry;
		return $registry->get('itemDb')->checkIfAlreadyExist($itemName);
	}

	public static function updateDetail($detail, $value, $itemId)
	{
		# code...
		global $registry;
		return $registry->get('itemDb')->updateDetail($detail, $value, $itemId);
	}

	public static function insert($itemId, $qty, $tbl){
		global $registry;
		return $registry->get('itemDb')->insert2($itemId, $qty, $tbl);
	}

	public static function checkIfAlreadyExistInTable($itemId, $tbl){
		global $registry;
		return $registry->get('itemDb')->checkIfAlreadyExistInTable($itemId, $tbl);
	}




	# End of Class
}
