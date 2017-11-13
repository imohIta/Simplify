<?php
namespace application\libs;
use \PDO;
use core\libs\Database as Db;
/**
*
*
*/ 
defined('ACCESS') || Error::exitApp();

class RoomDatabase extends Db{
	
	public function fetchDetails($roomId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchRoomDetails(:roomId)');
		$st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
		return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : false;
	}

	public function fetchType($roomType)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchRoomType(:roomType)');
		$st->bindValue(':roomType', $roomType, PDO::PARAM_INT);
		return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : false;
	}

	public function fetchByType($roomType)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchRoomsByType(:roomType)');
		$st->bindValue(':roomType', $roomType, PDO::PARAM_INT);
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : false;
	}

	public function fetchFreeByType($roomType)
	{
		# code..

		$st = $this->_driver->prepare('CALL sp_fetchFreeRoomsByType(:roomType)');
		$st->bindValue(':roomType', $roomType, PDO::PARAM_INT);
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : false;
	}

	public function fetchFree()
	{
		# code..

		$st = $this->_driver->prepare('CALL sp_fetchFreeRooms()');
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : false;
	}

	public function getOccupied($distinct)
	{
		#
		$query = (false === $distinct) ? 'CALL sp_getOccupiedRooms()'
				: 'select * from `guestRegister` group by
 guestId';
		$st = $this->_driver->prepare($query);
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : null;
	}

	public function getBad()
	{
		#
		$st = $this->_driver->prepare('CALL sp_getBadRooms()');
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : null;
	}

	public function getAppReserved()
	{
		#strtotime($row->rStartDate) <= strtotime(today()) && strtotime($row->rEndDate) >= strtotime(today())
		//$st = $this->_driver->prepare('CALL sp_getAppReservedRooms()');
		$st = $this->_driver->prepare('select * from `appReservations` where :date between `rStartDate` and `rEndDate`');
		$st->bindValue(':date', today(), PDO::PARAM_STR);
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : null;
	}

	public function getWebReserved()
	{
		#
		$st = $this->_driver->prepare('CALL sp_getWebReservedRooms()');
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : null;
	}

	# check if room is occupied or not
	public function checkStatus($roomId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_checkRoomStatus(:roomId)');
		$st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
		return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : null;
	}

	public function checkIfBad($roomId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_checkBadRoom(:roomId)');
		$st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
		$st->execute(); 
		$st->bindColumn('id', $id);
		$st->fetch(PDO::FETCH_ASSOC);
		return $id;
	}

	public function removeBad($roomId)
	{
		#
		$st = $this->_driver->prepare('CALL sp_removeBadRoom(:roomId)');
		$st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
		return $st->execute() ? true : false;
	}

	public function fetchAll()
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_fetchAllRooms()');
    	return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();

    }

    public function getCategories()
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_getRoomCategories()');
		return $st->execute()? $st->fetchAll(PDO::FETCH_OBJ) : false;
	}

    public function checkAvailablity($roomId, $beginDate, $endDate)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_checkRoomAvailablity(:roomId, :beginDate, :endDate)');
    	$st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
    	$st->bindValue(':beginDate', $beginDate, PDO::PARAM_STR);
    	$st->bindValue(':endDate', $endDate, PDO::PARAM_STR);
    	$st->execute();
    	$st->bindColumn('id', $id);
    	$st->fetch(PDO::FETCH_ASSOC);
    	$st->closeCursor();
    	return is_null($id) ? true : false ;
    	# return true if room is available or false if otherwise
    }

    public function fetchByNo($roomNo)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_fetchRoomByNo(:roomNo)');
    	$st->bindValue(':roomNo', $roomNo, PDO::PARAM_STR);
    	return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : array();
    }

    public function addBad(Array $data)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_addBadRoom(:dateAdded, :roomId, :reason)');
    	foreach ($data as $key => $value) {
    		if($key == 'roomId'){
    			$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
    		}else{
    			$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
    		}
    	}
    	return $st->execute() ? true : false;
    }

    public function exemptFromAutoBill($roomId, $date, $staffId)
    {
    	# code...
    	$st = $this->_driver->prepare('insert into `autoBillExemptions` ( date, roomId, staffId ) values ( :date, :roomId, :staffId )');
    	$st->bindValue(':date', $date, PDO::PARAM_STR);
    	$st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
    	$st->bindValue(':staffId', $staffId, PDO::PARAM_INT);
    	return $st->execute() ? true : false;
    }

    public function checkExemptedFromAutoBilling($roomId, $date)
    {
    	# code...
    	$st = $this->_driver->prepare('select `id` from `autoBillExemptions` where roomId = :roomId and date = :date');
    	$st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
    	$st->bindValue(':date', $date, PDO::PARAM_STR);
    	$st->execute();
    	$st->bindColumn('id', $id);
    	$st->fetch(PDO::FETCH_ASSOC);
    	return (is_null($id) || false === $id) ? false : true;
    }

    public function fetchAutoBillExemptions()
    {
    	# code...
    	$st = $this->_driver->prepare('select * from `autobillExemptions` order by `id` desc');
    	return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function inLateCheckOutList($roomId, $date)
    {
    	# code...
    	$st = $this->_driver->prepare('select `id` from `lateCheckOut` where roomId = :roomId and date = :date');
    	$st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
    	$st->bindValue(':date', $date, PDO::PARAM_STR);
    	$st->execute();
    	$st->bindColumn('id', $id);
    	$st->fetch(PDO::FETCH_ASSOC);
    	return (is_null($id) || false === $id) ? false : true;
    }

    public function addToLateCheckOut($roomId, $date, $time, $staffId)
    {
    	# code...
    	$st = $this->_driver->prepare('insert into `lateCheckOut` ( date, roomId, checkOutTime, staffId ) values ( :date, :roomId, :time, :staffId )');
    	$st->bindValue(':date', $date, PDO::PARAM_STR);
    	$st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
    	$st->bindValue(':time', $time, PDO::PARAM_INT);
    	$st->bindValue(':staffId', $staffId, PDO::PARAM_INT);
    	return $st->execute() ? true : false;
    }



#end of class	
}