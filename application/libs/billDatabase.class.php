<?php
namespace application\libs;
use \PDO;
use core\libs\Database as Db;
/**
* 
*
*/ 
defined('ACCESS') || Error::exitApp();

class BillDatabase extends Db{
	
	public function fetchBillById($id)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchBillById(:id)');
		$st->bindValue(':id', $id, PDO::PARAM_INT);
		return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : null;
	}

	 
	public function insertBillPayer(Array $values)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_InsertBillPayer(:guestId, :roomId, :billTypes)');
		$st->bindValue(':guestId', $values['guestId'], PDO::PARAM_INT);
		$st->bindValue(':roomId', $values['roomId'], PDO::PARAM_INT);
		$st->bindValue(':billTypes', $values['billTypes'], PDO::PARAM_STR);
		return $st->execute() ? true : false;
		
	}

	public function fetchByRoomId($roomId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchBillsByRoomId(:roomId)');
		$st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
	}

	public function selectBillPayerRooms($guestId, $roomId='')
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_selectBillPayerRooms(:guestId)');
		$st->bindValue(':guestId', $guestId, PDO::PARAM_INT);
		# $st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : null;
    }

	public function selectBillPayerOtherRooms($guestId, $roomId)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_selectBillPayerOtherRooms(:guestId, :roomId)');
		$st->bindValue(':guestId', $guestId, PDO::PARAM_INT);
		$st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : null;
    }

    /**
    * Select all guests that pay bills for a particular room
    */
    public function selectBillPayersForRoom($roomId)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_selectBillPayersForRoom(:roomId)');
		$st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : null;
    }

    public function updateBillPayer($guestId, $roomId, Array $billTypes)
    {
    	# get the bills types that guestId is paying for the roomId
    	$data = $this->getBillsCoveredByGuestForRoom($guestId, $roomId);

    	# if guestId is covering any billType for roomId
    	if(!empty($data) && !is_null($data)){

    		$bTypes = json_decode($data['billTypes'], true);
    		
    		# for each of billTypes ( which is the new BillTypes that guestId shud cover for roomId)
    		foreach ($billTypes as $key) {
    			# if key is not in billTypes currently covered by guestId for roomId
    			if(in_array($key, $bTypes) === false){
    				$bTypes[] = $key;
    			}
    		}

    		$newBillTypes = json_encode($bTypes);


    	}else{
    		$newBillTypes = json_encode($billTypes);
    	}
    	
    	return $this->updateBillPayer2($guestId, $roomId, $newBillTypes);

    }

    public function updateBillPayer2($guestId, $roomId, $billTypes)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_updateBillPayerBillTypes(:guestId, :roomId, :billTypes)');
		$st->bindValue(':guestId', $guestId, PDO::PARAM_INT);
        $st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
        $st->bindValue(':billTypes', $billTypes, PDO::PARAM_STR);
        return $st->execute() ? true : false;
    }

    public function getBillsCoveredByGuestForRoom($guestId, $roomId)
    {
    	# get the bills types that guestId is paying for the roomId
    	$st = $this->_driver->prepare('CALL sp_getBIllsCoveredByGuestForRoom(:guestId, :roomId)');
    	$st->bindValue(':guestId', $guestId, PDO::PARAM_INT);
    	$st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
    	return $st->execute() ? $st->fetch(PDO::FETCH_ASSOC) : null;
    }

    public function changeBillPayerRoom($params)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_changeBillPayerRoom(:guestId, :oldRoomId, :newRoomId)');
    	foreach ($params as $key => $value) {
    		# code...
    		$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
    	}
    	return $st->execute() ? true : false;

    }

    public function changeGuestBillsRoomId(Array $data)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_changeGuestBillsRoomId(:guestId, :oldRoom, :newRoom)');
		$st->bindValue(':guestId', $data['guestId'], PDO::PARAM_INT);
		$st->bindValue(':oldRoom', $data['oldRoomId'], PDO::PARAM_INT);
		$st->bindValue(':newRoom', $data['newRoomId'], PDO::PARAM_INT);
		return $st->execute() ? true : false; 
		
	}


    public function delete($transId)
    {
        # code...
        $st = $this->_driver->prepare('delete from `guestBills` where `transId` = :transId');
        $st->bindValue(':transId', $transId, PDO::PARAM_STR);
        return $st->execute() ? true : false;
    }




#end of class	
}