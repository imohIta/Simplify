<?php
namespace application\libs;
use \PDO;
use core\libs\Database as Db;
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class ItemDatabase extends Db{

	public function fetchDetails($itemId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchItemDetails(:itemId)');
		$st->bindValue(':itemId', $itemId, PDO::PARAM_INT);
		return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : false;
	}

	public function fetchType($typeId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchItemType(:typeId)');
		$st->bindValue(':typeId', $typeId, PDO::PARAM_INT);
		$st->execute();
		$st->bindColumn('name', $name);
		$st->fetch(PDO::FETCH_ASSOC);
		return $name;

	}

	public function fetchUnit($unitId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchItemUnit(:unitId)');
		$st->bindValue(':unitId', $unitId, PDO::PARAM_INT);
		$st->execute();
		$st->bindColumn('name', $name);
		$st->fetch(PDO::FETCH_ASSOC);
		return $name;

	}

	public function getQtyInStock($itemId, $tbl)
	{
		# code...
		$st = $this->_driver->prepare('select `qtyInStock` from ' . $tbl . ' where `itemId` = :itemId');
		$st->bindValue(':itemId', $itemId, PDO::PARAM_INT);
		$st->execute();
		$st->bindColumn('qtyInStock', $qty);
		$st->fetch(PDO::FETCH_OBJ);
		return (is_null($qty) || $qty === false) ? null : $qty;
	}

	public function getSellingPrice($itemId, $tbl)
	{
		# code...
		$st = $this->_driver->prepare('select `sellingPrice` from ' . $tbl . ' where `itemId` = :itemId');
		$st->bindValue(':itemId', $itemId, PDO::PARAM_INT);
		$st->execute();
		$st->bindColumn('sellingPrice', $price);
		$st->fetch(PDO::FETCH_OBJ);
		return (is_null($price) || $price === false) ? 0 : $price;
	}

	public function reducePosItemFromStock($itemId, $qty, $tbl)
	{
		# code...
		$st = $this->_driver->prepare('update `' . $tbl . '` set `qtyInStock` = `qtyInStock` - :qty where `itemId` = :itemId');
		$st->bindValue(':qty', $qty, PDO::PARAM_INT);
		$st->bindValue(':itemId', $itemId, PDO::PARAM_INT);
		return $st->execute() ? true : false;

	}

	public function increasePosItemInStock($itemId, $qty, $tbl)
	{
		# code...
		$st = $this->_driver->prepare('update `' . $tbl . '` set `qtyInStock` = `qtyInStock` + :qty where `itemId` = :itemId');
		$st->bindValue(':qty', $qty, PDO::PARAM_INT);
		$st->bindValue(':itemId', $itemId, PDO::PARAM_INT);
		return $st->execute() ? true : false;

	}

	public function fetchTypes()
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchItemTypes()');
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : false;
	}

	public function fetchUnits()
	{
		# code...
		$st = $this->_driver->prepare('select * from itemUnits');
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : false;
	}

	public function fetchAll($tbl, $xcludeFinished)
	{
		# code...
		global $registry;
		//var_dump($xcludeFinished); die;

		$query = 'select * from ' . $tbl;
		if($xcludeFinished){
			$query .= ' where `qtyInStock` != 0';
		}
		$st = $this->_driver->prepare($query);
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
	}

	public function fetchReduced($tbl, $threashold)
	{
		# code...
		global $registry;

		$query = 'select * from `' . $tbl . '` where `qtyInStock` < ' . $threashold;
		$st = $this->_driver->prepare($query);
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
	}

	public function addNew(Array $data)
	{
		# insert into Items
		$st = $this->_driver->prepare('insert into `items` ( name, typeId, unitId ) values ( :name, :type, :unit )');
		$st->bindValue(':name', $data['name'], PDO::PARAM_STR);
		$st->bindValue(':type', $data['type'], PDO::PARAM_INT);
		$st->bindValue(':unit', $data['unit'], PDO::PARAM_INT);
		$st->execute();
		$id = $this->_driver->lastInsertId();

		# insert into Store
		$t = $this->_driver->prepare('insert into store ( itemId, qtyInStock ) values ( :itemId, :qty )');
		$t->bindValue(':itemId', $id, PDO::PARAM_INT);
		$t->bindValue(':qty', 0, PDO::PARAM_INT);
		return $t->execute() ? true : false;


	}

	public function checkIfAlreadyExist($itemName)
	{
		# code...
		$st = $this->_driver->prepare('select `id` from `items` where `name` = :name');
		$st->bindValue(':name', $itemName, PDO::PARAM_STR);
		$st->execute();
		$st->bindColumn('id', $id);
		$st->fetch(PDO::FETCH_ASSOC);
		return (is_null($id) || $id === false) ? false : true;
	}


	public function addNewPosItem(Array $data)
	{
		# code...
		if(strtolower($data['tbl']) == 'kitchenstk'){
			$st = $this->_driver->prepare('insert into `' . $data['tbl'] . '` ( itemId, qtyInStock ) values ( :itemId, :qty )');
		}else{
			$st = $this->_driver->prepare('insert into `' . $data['tbl'] . '` ( itemId, qtyInStock, sellingPrice ) values ( :itemId, :qty, :price )');
		}
		$st->bindValue('itemId', $data['item'], PDO::PARAM_INT);
		$st->bindValue('qty', $data['qty'], PDO::PARAM_INT);
		if(strtolower($data['tbl']) != 'kitchenstk'){
			$st->bindValue('price', $data['price'], PDO::PARAM_INT);
		}
		return $st->execute() ? true : false;
	}
	
	public function updateDetail($detail, $value, $itemId)
	{
		# code...
		$query = 'update `items` set `' . $detail . '` = :value where `id` = :id';
		$st = $this->_driver->prepare($query);
		$st->bindValue(':value', $value, PDO::PARAM_STR);
		$st->bindValue(':id', $itemId, PDO::PARAM_INT);
		return $st->execute() ? true : false;
	}

	public function updatePosDetail($tbl, $price, $itemId)
	{
		# code...
		$query = 'update `' . $tbl . '` set `sellingPrice` = :price where `itemId` = :id';
		$st = $this->_driver->prepare($query);
		$st->bindValue(':price', $price, PDO::PARAM_INT);
		$st->bindValue(':id', $itemId, PDO::PARAM_INT);
		return $st->execute() ? true : false;
	}
	
	public function updatePosDetail2($tbl, $detail, $value, $itemId)
	{
		# code...
		$query = 'update `' . $tbl . '` set `' . $detail . '` = :value where `itemId` = :id';
		$st = $this->_driver->prepare($query);
		$st->bindValue(':value', $value, PDO::PARAM_INT);
		$st->bindValue(':id', $itemId, PDO::PARAM_INT);
		return $st->execute() ? true : false;
	}

	public function fetchSold($id, $startTime, $endTime)
	{
		# code...
		$st = $this->_driver->prepare('select sum(qty) as total from `sales` where object = 2 and objectId = :itemId and ( time between :startTime and :endTime )');
		$st->bindValue(':itemId', $id, PDO::PARAM_INT);
		$st->bindValue(':startTime', $startTime, PDO::PARAM_STR);
		$st->bindValue(':endTime', $endTime, PDO::PARAM_STR);
		$st->execute();
		$st->bindColumn('total', $total);
		$st->fetch(PDO::FETCH_ASSOC);
		return !is_null($total) ? $total : 0;
		

	}

	public function fetchSold2($id, $startTime, $endTime)
	{
		# code...
		$st = $this->_driver->prepare('select sum(qty) as total from `sales` where object = 1 and objectId = :itemId and ( time between :startTime and
:endTime )');
		$st->bindValue(':itemId', $id, PDO::PARAM_INT);
		$st->bindValue(':startTime', $startTime, PDO::PARAM_STR);
		$st->bindValue(':endTime', $endTime, PDO::PARAM_STR);
		$st->execute();
		$st->bindColumn('total', $total);
		$st->fetch(PDO::FETCH_ASSOC);
		return !is_null($total) ? $total : 0;


	}


	public function fetchRequisitions($id, $startTime, $endTime, $priv)
	{
		# code...
		$st = $this->_driver->prepare('select sum(qty) as total from `requisitions` where itemId = :itemId and issued = 1 and privilege = :priv and ( time between :startTime and :endTime )');
		$st->bindValue(':itemId', $id, PDO::PARAM_INT);
		$st->bindValue(':startTime', $startTime, PDO::PARAM_STR);
		$st->bindValue(':endTime', $endTime, PDO::PARAM_STR);
		$st->bindValue(':priv', $priv, PDO::PARAM_INT);
		$st->execute();
		$st->bindColumn('total', $total);
		$st->fetch(PDO::FETCH_ASSOC);
		return !is_null($total) && false !== $total ? $total : 0;

	}

	public function fetchAdditions($date)
	{
		# code...
		$st = $this->_driver->prepare('select * from stockPurchases where date = :date and approved = 1');
		$st->bindValue(':date', $date, PDO::PARAM_STR);
		return $st->execute() ? $st->fetchAll() : null;
	}

	public function fetchTotalIssuedRequisitions($itemId, $startTime, $endTime)
	{
		# code...
		$st = $this->_driver->prepare('select sum(qty) as total from `requisitions` where itemId = :itemId and issued = 1 and ( time between :startTime and :endTime )');
		$st->bindValue(':itemId', $itemId, PDO::PARAM_INT);
		$st->bindValue(':startTime', $startTime, PDO::PARAM_STR);
		$st->bindValue(':endTime', $endTime, PDO::PARAM_STR);
		$st->execute();
		$st->bindColumn('total', $total);
		$st->fetch(PDO::FETCH_ASSOC);
		return !is_null($total) ? $total : 0;
	}

	public function fetchIssuedRequisitionDetails($itemId, $startTime, $endTime)
	{
		# code...
		$st = $this->_driver->prepare('select * from `requisitions` where itemId = :itemId and issued = 1 and ( time between :startTime and :endTime )');
		$st->bindValue(':itemId', $itemId, PDO::PARAM_INT);
		$st->bindValue(':startTime', $startTime, PDO::PARAM_STR);
		$st->bindValue(':endTime', $endTime, PDO::PARAM_STR);
		return $st->execute() ? $st->fetchAll() : array();
	}


	# this function adds new item into kitchen ot housekeeping stock
	public function insert2($itemId, $qty, $tbl)
	{
		$query = 'insert into ' . $tbl . ' ( itemId, qtyInStock ) value ( :itemId, :qty )';
		$st = $this->_driver->prepare($query);
		$st->bindValue(':itemId',$itemId, PDO::PARAM_INT );
		$st->bindValue(':qty',$qty, PDO::PARAM_INT );
		return $st->execute() ? true : false;
	}

	public function checkIfAlreadyExistInTable($itemId, $tbl){
		$query = 'select id from ' . $tbl . ' where itemId = :itemId';
		$st = $this->_driver->prepare($query);
		$st->bindValue(':itemId', $itemId, PDO::PARAM_INT);
		$st->execute();
		$st->bindColumn('id', $id);
		$st->fetch();
		return is_null($id) || false === $id ? false : true;
	}

	public function deleteStockItem($itemId){

		$st = $this->_driver->prepare('delete from items where id = :itemId');
		$st->bindValue(':itemId', $itemId, PDO::PARAM_INT);
		return $st->execute() ? true : false;

	}

	public function deleteMenuItem($menuId){

		$st = $this->_driver->prepare('delete from menu where id = :menuId');
		$st->bindValue(':menuId', $menuId, PDO::PARAM_INT);
		return $st->execute() ? true : false;

	}


#end of class
}
