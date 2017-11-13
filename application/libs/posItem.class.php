<?php
use application\libs\TransactionDatabase as Db;
defined('ACCESS') || Error::exitApp();

/**
* Item Decorator
* Recieves Item object during construction and add xtra functionalities to it to suit itself
*/
class PosItem extends FuniObject
{
	public $id;
	public $typeId;
	public $type;
	public $name;
	public $unitId;
	public $unit;
	public $qtyInStock;
	public $price;

	private $_db;
	private $_tbl;

	function __construct(Item $item, $tbl = '')
	{
		global $registry;
		$thisUser = unserialize($registry->get('session')->read('thisUser'));
		$this->_db = $registry->get('itemDb');
		$this->_tbl = ($tbl) ? $tbl : $thisUser->tbl;

		$this->_item = $item;
		$this->id = $item->id;
		$this->typeId = $item->typeId;
		$this->type = $item->type;
		$this->name = $item->name;
		$this->unitId = $item->unitId;
		$this->unit = $item->unit;
		$this->qtyInStock = $this->_db->getQtyInStock($this->id, $this->_tbl);
		if($this->_tbl != 'kitchenStk' && $this->_tbl != 'store' && $this->_tbl != 'house_keepingStk'){
			$this->price = $this->_db->getSellingPrice($this->id, $this->_tbl);
		}
	}

	public function reduceFromStock($qty)
	{
		# code...
		global $registry;

		$this->_db->reducePosItemFromStock($this->id, $qty, $this->_tbl);
		$this->qtyInStock -= $qty;

	}

	public function IncreaseStockQty($qty)
	{
		# code...
		global $registry;

		$this->_db->increasePosItemInStock($this->id, $qty, $this->_tbl);
		$this->qtyInStock += $qty;
	}

	public function fetchSold()
	{
		# code...
		global $registry;
		$session = $registry->get('session');
		$thisUser = unserialize($registry->get('session')->read('thisUser'));

		$shiftTimes = $registry->get('db')->fetchShiftTimes(today());

		//$beginTime = is_null($shiftTimes) ? $session->read('shiftBeginTime') : $shiftTimes->beginTime;


		return $this->_db->fetchSold($this->id, $shiftTimes->beginTime, $shiftTimes->endTime);


	}


	public function fetchRequisitions($priv)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$shiftTimes = $registry->get('db')->fetchShiftTimes(today());

		//var_dump(1453448596, $shiftTimes->beginTime, $shiftTimes->endTime, $priv); die;
		//var_dump($this->_db->fetchRequisitions($this->id, $shiftTimes->beginTime, $shiftTimes->endTime, $priv)); die;

		return $this->_db->fetchRequisitions($this->id, $shiftTimes->beginTime, $shiftTimes->endTime, $priv);
	}

	/************************************
				STATIC FUNCTIONS
	*************************************/


	public static function addNew(Array $data)
	{
		# code...
		global $registry;
		return $registry->get('itemDb')->addNewPosItem($data);
	}
	

	public static function updateDetail($tbl, $price, $itemId)
	{
		# code...
		global $registry;
		return $registry->get('itemDb')->updatePosDetail($tbl, $price, $itemId);
	}
	
	public static function updateDetail2($tbl, $detail, $value, $itemId)
	{
		# code...
		global $registry;
		return $registry->get('itemDb')->updatePosDetail2($tbl, $detail, $value, $itemId);
	}

	public static function fetchAdditions($date, $itemId)
	{
		# code...
		global $registry;

		# try to fetch additions
		$additions = $registry->get('itemDb')->fetchAdditions($date, $itemId);
		if(is_null($additions) || $additions === false){
			return 0;
		}

		$qty= 0;
		foreach ($additions as $row) {
			# code...
			
			foreach (json_decode($row->purchase) as $key) {
				# code...
				//var_dump($key);
				if($key->itemId == $itemId){
					$qty += $key->qty;
				}
			}
		}
		return $qty;
	}

	public static function fetchTotalIssuedRequisitions($itemId, $date)
	{
		# code...
		global $registry;
		$shiftTimes = $registry->get('db')->fetchShiftTimes(today());


		return $registry->get('itemDb')->fetchTotalIssuedRequisitions($itemId, $shiftTimes->beginTime, $shiftTimes->endTime);
	}

	public static function fetchIssuedRequisitionDetails($itemId)
	{
		# code...
		global $registry;
		$shiftTimes = $registry->get('db')->fetchShiftTimes(today());

		return $registry->get('itemDb')->fetchIssuedRequisitionDetails($itemId, $shiftTimes->beginTime, $shiftTimes->endTime);
	}






#end of class
}
