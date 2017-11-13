<?php
namespace application\libs;
use \PDO;
use core\libs\Database as Db;
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class Database extends Db{
	
	public function fetchUserDetails($userId)
	{
		#
		$st = $this->_driver->prepare('CALL sp_fetchUserDetails(:userId)');
		$st->bindValue(':userId', $userId, PDO::PARAM_INT);
		return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : null;
	}

	/*public function fetchGuestDetails($guestId)
	{
		#
		$st = $this->_driver->prepare('CALL sp_fetchGuestDetails(:userId)');
		$st->bindValue(':userId', $guestId, PDO::PARAM_INT);
		return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : null;
	}*/


	public function getUserRole($priv)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_getUserRole(:priv)');
		$st->bindValue(':priv', $priv, PDO::PARAM_INT);
		$st->execute();
		$st->bindColumn('role', $role);
		$st->fetch(PDO::FETCH_ASSOC);
		$st = null;
		return $role;
	}

	public function getPwdHash($username)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_getPwdHash(:username)');
		$st->bindValue(':username', $username, PDO::PARAM_STR);
		$st->execute();
		$st->bindColumn('hash', $hash);
		$st->bindColumn('id', $id);
		$st->fetch(PDO::FETCH_ASSOC);
		$st = null;
		return array('hash' => $hash, 'id' => $id);
	}

	public function updatePwdHash($id, $newHash)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_updatePwdHash(:id, :newHash)');
		$st->bindValue(':id', $id, PDO::PARAM_INT);
		$st->bindValue(':newHash', $newHash, PDO::PARAM_STR);
		return $st->execute() ? true : false;
	}

	public function getRoomCategories()
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_getRoomCategories()');
		return $st->execute()? $st->fetchAll(PDO::FETCH_OBJ) : false;
	}

	/*public function fetchRoomDetails($roomId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchRoomDetails(:roomId)');
		$st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
		return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : false;
	}*/

	/*public function fetchRoomType($roomType)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchRoomType(:roomType)');
		$st->bindValue(':roomType', $roomType, PDO::PARAM_INT);
		return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : false;
	}

	public function fetchRoomsByType($roomType)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchRoomsByType(:roomType)');
		$st->bindValue(':roomType', $roomType, PDO::PARAM_INT);
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : false;
	}*/

	/*public function getOccupiedRooms()
	{
		#
		$st = $this->_driver->prepare('CALL sp_getOccupiedRooms()');
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : null;
	}

	# check if room is occupied or not
	public function checkRoomStatus($roomId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_checkRoomStatus(:roomId)');
		$st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
		return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : null;
	}

	public function checkBadRoom($roomId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_checkBadRoom(:roomId)');
		$st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
		$st->execute(); 
		$st->bindColumn('id', $id);
		return $id;
	}*/


	# this function will fetch all rooms that are not already occupied or has not been fully booked for today
	public function fetchFreeRoomsByType($roomType)
	{
		# code..

		$st = $this->_driver->prepare('CALL sp_fetchFreeRoomsByType(:roomType)');
		$st->bindValue(':roomType', $roomType, PDO::PARAM_INT);
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : false;
	}

	/*public function fetchGuestDetailsByPhone($phone)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchGuestDetailsByPhone(:phone)');
		$st->bindValue(':phone', $phone, PDO::PARAM_STR);
		return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : false;
	}*/

	/*public function getGuestOutstandingBal($phone)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_getGuestBal(:phone)');
		$st->bindValue(':phone', $phone, PDO::PARAM_STR);
		return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : false;
	}*/

	public function fetchUserTransactions($userId, $priv)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchUserTransactions(:userId, :priv)');
		$st->bindValue(':userId', $userId, PDO::PARAM_INT);
		$st->bindValue(':priv', $priv, PDO::PARAM_INT);
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : false;
	}


	

	/*public function getGuestCheckInInfo($roomId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_getGuestCheckInInfo(:roomId)');
		$st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
		return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : null;
	}*/

	/*public function getGuestCheckInInfo2($guestId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_getGuestCheckInInfo2(:guestId)');
		$st->bindValue(':guestId', $guestId, PDO::PARAM_INT);
		return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : null;
	}*/
	
	/*public function fetchGuestBills($guestId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchGuestBills(:guestId)');
		$st->bindValue(':guestId', $guestId, PDO::PARAM_INT);
		return $st->execute() ? $st->fetchAll(PDO::FETCH_ASSOC) : null;
	}
*/
	/*public function fetchGuestPayments($guestId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchGuestPayments(:guestId)');
		$st->bindValue(':guestId', $guestId, PDO::PARAM_INT);
		return $st->execute() ? $st->fetchAll(PDO::FETCH_ASSOC) : null;
	}*/

	/*public function deleteGuestBills($guestId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_deleteGuestBills(:guestId)');
		$st->bindValue(':guestId', $guestId,PDO::PARAM_INT);
		return $st->execute() ? true : false;
	}*/

	/*public function deleteGuestPayments($guestId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_deleteGuestPayments(:guestId)');
		$st->bindValue(':guestId', $guestId, PDO::PARAM_INT);
		return $st->execute() ? true : false;
	}*/

	/*public function fetchTotalGuestBill($guestId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchTotalGuestBill(:guestId)');
		$st->bindValue(':guestId', $guestId);
		$st->execute(); 
		$st->bindColumn('total', $total);
		return $st->fetch(PDO::FETCH_ASSOC) ? $total : null;
	}*/

	/*public function fetchTotalGuestPayment($guestId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchTotalGuestPayment(:guestId)');
		$st->bindValue(':guestId', $guestId);
		$st->execute(); 
		$st->bindColumn('total', $total);
		return $st->fetch(PDO::FETCH_ASSOC) ? $total : null;
	}*/

	/*public function changeGuestBillsGuestId(Array $data)
	{


		$st = $this->_driver->prepare('CALL sp_changeGuestBillsGuestId(:id, :newGuestId)');
		
		# code...
		foreach ($this->fetchBillsByRoomId($data['roomId']) as $key) {
			
			# if date is latter than or equal to the start date
			if(strtotime($key->date) >= strtotime($data['startDate'])){

				# check billTypes to cover

				# if billtypes is All
				if($data['billTypes'] == 'All'){
				
					# update bill guest id
					$st->bindValue(':id', $key->id, PDO::PARAM_INT);
					$st->bindValue(':newGuestId', $data['newGuestId'], PDO::PARAM_INT);
					$st->execute();
			    

			    # if bill Type is room charge
			    }elseif($data['billTypes'] == 'roomCharge' && $key->billType == 2){
			    	//echo $key['id']; die;
					# update bill guest id
					$st->bindValue(':id', $key->id, PDO::PARAM_INT);
					$st->bindValue(':newGuestId', $data['newGuestId'], PDO::PARAM_INT);
					$st->execute();
			    	

			    }elseif($data['billTypes'] == 'POSUnits' && $key->billType > 2){
			    	//echo $key['id']; die;
		    		# update bill guest id
					$st->bindValue(':id', $key->id, PDO::PARAM_INT);
					$st->bindValue(':newGuestId', $data['newGuestId'], PDO::PARAM_INT);
					$st->execute();
			    }
			}

		}

	}*/

	/*public function fetchBillsByRoomId($roomId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchBillsByRoomId(:roomId)');
		$st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
	}*/

	/*public function changeGuestBillsRoomId(Array $data)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_changeGuestBillsRoomId(:guestId, :oldRoom, :newRoom)');
		$st->bindValue(':guestId', $data['guestId'], PDO::PARAM_INT);
		$st->bindValue(':oldRoom', $data['oldRoomId'], PDO::PARAM_INT);
		$st->bindValue(':newRoom', $data['newRoomId'], PDO::PARAM_INT);
		return $st->execute() ? true : false; 
		
	}*/

	/*public function addGuestBill(Array $data)
	{
		//var_dump($data); die;
		global $registry;
		$thisUser = unserialize($registry->get('session')->read('thisUser'));

		#bills
		$st = $this->_driver->prepare('CALL sp_insertGuestBill(:date, :guestId, :roomId, :transId, :amt, :billType, :details)');
        foreach ($data as $key => $value) {
			if(in_array($key, array('amt', 'guestId', 'billType', 'roomId')) !== false){
				$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
			}else{
				$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
			}
		}

		$st->execute();
		$st->bindColumn('id', $id);
		$st->fetch(PDO::FETCH_ASSOC);
		$st->closeCursor();
		$st = null;
		return $this->addTransaction(array(
								'date' => $data['date'],
								'time' => now(),
								'transId' => $data['transId'],
								'transType' => 1,
								'src' => json_encode(array('tbl' => 'guestBills', 'id' => $id)),
								'details' => json_encode(array(
														'type' => 'Guest Bill',
														'guestId' => $data['guestId'],
														'desc' => $data['details'],
														'amt' => $data['amt'])),
								'staffId' => $thisUser->id,
								'privilege' => $thisUser->privilege
								)) ? true :false;
	}*/

	/*public function addGuestPayment(Array $data)
	{
		global $registry;
		$thisUser = unserialize($registry->get('session')->read('thisUser'));

		$st = $this->_driver->prepare('CALL sp_insertGuestPayment(:date, :guestId, :transId, :amt, :details)');
		foreach ($data as $key => $value) {
			if($key == "amt" || $key == "guestId"){
				$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
			}else{
				$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
			}
		}
		$st->execute();
		$st->bindColumn('id', $id);
		$st->fetch(PDO::FETCH_ASSOC);
		$st->closeCursor();
		$st = null;
		return $this->addTransaction(array(
								'date' => $data['date'],
								'time' => now(),
								'transId' => $data['transId'],
								'transType' => 2,
								'src' => json_encode(array('tbl' => 'guestPayments', 'id' => $id)),
								'details' => json_encode(array(
														'type' => 'Guest Payment',
														'guestId' => $data['guestId'],
														'desc' => $data['details'],
														'amt' => $data['amt'])),
								'staffId' => $thisUser->id,
								'privilege' => $thisUser->privilege
								)) ? true :false;

	}*/
    
    /*public function addTransaction(Array $data)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_insertTransaction(:date, :time, :transId, :transType, :src, :details, :staffId, :privilege )');
		foreach ($data as $key => $value) {
			if(in_array($key, array('transType', 'staffId', 'privilege')) !== false){
				$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
			}else{
				$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
			}
		}
        return $st->execute() ? true : false;
    }*/

    
    /*public function addGuestRefund(Array $data){
    	global $registry;
		$thisUser = unserialize($registry->get('session')->read('thisUser'));

		#payment
		$st = $this->_driver->prepare('CALL sp_insertGuestRefund(:date, :guestId, :transId, :amt)');
		foreach ($data as $key => $value) {
			if($key == "amt" || $key == "guestId"){
				$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
			}else{
				$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
			}
		}
		$st->execute();
		$st->bindColumn('id', $id);
		$st->fetch(PDO::FETCH_ASSOC);
		$st->closeCursor();
		$st = null;
		return $this->addTransaction(array(
								'date' => $data['date'],
								'time' => now(),
								'transId' => $data['transId'],
								'transType' => 7,
								'src' => json_encode(array('tbl' => 'guestRefunds', 'id' => $id)),
								'details' => json_encode(array(
														'type' => 'Guest Refund',
														'guestId' => $data['guestId'],
														'desc' => 'Excess Payment Refund',
														'amt' => $data['amt'])),
								'staffId' => $thisUser->id,
								'privilege' => $thisUser->privilege
								)) ? true :false;
    }*/

    /*public function addGuestOutstandingBal(Array $data)
    {
    	$st = $this->_driver->prepare('CALL sp_insertGuestOutstandingBal(:phone, :amt)');
		foreach ($data as $key => $value) {
			if($key == "amt"){
				$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
			}else{
				$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
			}
		}
        return $st->execute() ? true : false;
    }

    public function addGuestCredit(Array $data)
    {
    	global $registry;
		$thisUser = unserialize($registry->get('session')->read('thisUser'));

		# if the function is bieng called from guestModell::submitCheckout
		if(isset($data[1])){
           $guest = new \Guest($data[0]);
           $amt = $data[1];
           $data = array(
           				'date' => today(),
           				'guestId' => $guest->id,
           				'guestPhone' => $guest->phone,
           				'transId' => generateTransId(),
           				'amt' => $amt,
           				'details' => 'From Credit Check out'
           				);
		}
		
    	$st = $this->_driver->prepare('CALL sp_insertGuestCredit(:date, :guestId, :guestPhone, :transId, :amt, :details)');
		foreach ($data as $key => $value) {
			if($key == "amt" || $key == "guestId"){
				$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
			}else{
				$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
			}
		}
		$st->execute();
		$st->bindColumn('id', $id);
		$st->fetch(PDO::FETCH_ASSOC);
		$st->closeCursor();
		$st = null;
        return $this->addTransaction(array(
								'date' => $data['date'],
								'time' => now(),
								'transId' => $data['transId'],
								'transType' => 8,
								'src' => json_encode(array('tbl' => 'guestCredits', 'id' => $id)),
								'details' => json_encode(array(
														'type' => 'Guest Credit',
														'guestId' => $data['guestId'],
														'desc' => $data['details'],
														'amt' => $data['amt'])),
								'staffId' => $thisUser->id,
								'privilege' => $thisUser->privilege
								)) ? true : false;

    }*/


    /*public function addGuestCreditPayment(Array $data)
    {
    	# code...
    	global $registry;
		$thisUser = unserialize($registry->get('session')->read('thisUser'));

    	$st = $this->_driver->prepare('CALL sp_insertGuestCreditPayment(:date, :guestId, :guestPhone, :transId, :amt, :details)');
		foreach ($data as $key => $value) {
			if($key == "amt" || $key == "guestId"){
				$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
			}else{
				$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
			}
		}
        $st->execute();
		$st->bindColumn('id', $id);
		$st->fetch(PDO::FETCH_ASSOC);
		$st->closeCursor();
		$st = null;
        return $this->addTransaction(array(
								'date' => $data['date'],
								'time' => now(),
								'transId' => $data['transId'],
								'transType' => 9,
								'src' => json_encode(array('tbl' => 'guestCreditPayments', 'id' => $id)),
								'details' => json_encode(array(
															'type' => 'Guest Credit Payment',
															'guestId' => $data['guestId'],
															'desc' => $data['details'],
															'amt' => $data['amt']
															)),
								'staffId' => $thisUser->id,
								'privilege' => $thisUser->privilege
								)) ? true : false;

    }*/

    /*public function addGuestStayActivity(Array $data)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_insertGuestStayInfo(:guestPhone, :stayInfo, :bills, :payments)');
		foreach ($data as $key => $value) {
			$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
		}
        return $st->execute() ? true : false;
    }

    public function deleteGuestFromRegister($guestId)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_deleteGuestFromRegister(:guestId)');
		$st->bindValue(':guestId', $guestId, PDO::PARAM_INT);
        return $st->execute() ? true : false;
    }*/

    /**
    * Select all rooms that guest id is paying bills for
    */
    /*public function selectBillPayerRooms($guestId, $roomId='')
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_selectBillPayerRooms(:guestId)');
		$st->bindValue(':guestId', $guestId, PDO::PARAM_INT);
		# $st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : null;
    }*/

    /*
    * Select all the rooms that guest Id is paying bills for excluding roomId
    */
    /*public function selectBillPayerOtherRooms($guestId, $roomId)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_selectBillPayerOtherRooms(:guestId, :roomId)');
		$st->bindValue(':guestId', $guestId, PDO::PARAM_INT);
		$st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : null;
    }*/

    /**
    * Select all guests that pay bills for a particular room
    */
    /*public function selectBillPayersForRoom($roomId)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_selectBillPayersForRoom(:roomId)');
		$st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : null;
    }*/

    /*public function addGuestAsBillPayer(Array $values)
    {
    	# code...
		$st = $this->_driver->prepare('CALL sp_InsertBillPayer(:guestId, :roomId, :billTypes)');
		$st->bindValue(':guestId', $values['guestId'], PDO::PARAM_INT);
		$st->bindValue(':roomId', $values['roomId'], PDO::PARAM_INT);
		$st->bindValue(':billTypes', $values['billTypes'], PDO::PARAM_STR);
		return $st->execute() ? true : false;
    }

    public function deleteGuestFromBillPayers($guestId, $roomId)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_deleteGuestFromBillPayers(:guestId, :roomId)');
		$st->bindValue(':guestId', $guestId, PDO::PARAM_INT);
		$st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
        return $st->execute() ? true : false;
    }*/

    /**
    * This function will update billPayers and make guestId cover billTypes for roomId
    *
    */
    /*public function updateBillPayer($guestId, $roomId, Array $billTypes)
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

    }*/
    
    /*public function updateGuestRoomInRegister($params)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_updateGuestRoomInRegister(:guestId, :oldRoomId, :newRoomId)');
    	foreach ($params as $key => $value) {
    		# code...
    		$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
    	}
    	return $st->execute() ? true : false;

    }*/

    /*public function updateBillsForGuest(Array $params)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_updateBillsForGuest(:date, :guestId, :amt, :billType)');
    	foreach ($params as $key => $value) {
    		# code...
    		if($key == 'date'){
    			$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
    		}else{
    			$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
    		}
    	}
    	$st->bindValue(':billType', 2);
    	$st->execute();
    	$st->bindColumn('tId', $transId);
    	$st->fetch(PDO::FETCH_ASSOC);
    	return $transId;

    }*/

   /* public function getTransactionById($transId)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_getTransactionById(:transId)');
    	$st->bindValue(':transId', $transId, PDO::PARAM_STR);
    	return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : null;
    }*/

   /* public function updateTransactionDetails(Array $params)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_updateTransactionDetails(:transId, :details)');
    	foreach ($params as $key => $value) {
    		# code...
    		$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
    	}
    	return $st->execute() ? true : false;
    }*/



    /*public function updateGuestDiscount(Array $params)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_updateGuestDiscount(:guestId, :roomId, :discount)');
    	foreach ($params as $key => $value) {
    		# code...
    		$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
    	}
    	return $st->execute() ? true : false;
    }

    public function fetchPreviousGuestDetails($guestPhone)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_fetchPreviousGuestDetails(:phone)');
    	$st->bindValue(':phone', $guestPhone, PDO::PARAM_STR);
    	return $st->execute() ? $st->fetchAll(PDO::FETCH_ASSOC) : array();
    }*/

    /*public function fetchGuestNameByPhone($guestPhone)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_fetchGuestNameByPhone(:guestPhone)');
    	$st->bindValue(':guestPhone', $guestPhone, PDO::PARAM_STR);
    	$st->execute();
    	$st->bindColumn('name', $name);
    	$st->fetch(PDO::FETCH_ASSOC);
    	$st = null;
    	return $name;
    }*/

    /*public function fetchPreviousGuestCredits($guestPhone)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_fetchPreviousGuestCredits(:guestPhone)');
    	$st->bindValue(':guestPhone', $guestPhone, PDO::PARAM_STR);
    	return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array() ;
    }

    public function fetchPreviousGuestPayments($guestPhone)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_fetchPreviousGuestCreditPayments(:guestPhone)');
    	$st->bindValue(':guestPhone', $guestPhone, PDO::PARAM_STR);
    	return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array() ;
    }*/

    public function fetchAllRooms()
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_fetchAllRooms()');
    	return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();

    }

    public function checkRoomAvailablity($roomId, $beginDate, $endDate)
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

    public function addReservation(Array $data)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_addReservation(:date, :startDate, :endDate, :guestName, :guestPhone, :roomId, :reserveId)');
    	foreach ($data as $key => $value) {
    		if($key == 'roomId'){
    			$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
    		}else{
    			$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
    		}
    	}
    	return $st->execute() ? true : false;
    }

    public function updateReservation(Array $data)
    {
    	# code...
    	if($data['src'] == 'app'){
    		$st = $this->_driver->prepare('CALL sp_editAppReservation(:startDate, :endDate, :guestName, :guestPhone, :roomId, :reserveId)');
    	}else{
    		$st = $this->_driver->prepare('CALL sp_editWebReservation(:startDate, :endDate, :guestName, :guestPhone, :roomId, :reserveId)');
    	}
    	foreach ($data as $key => $value) {
    		if($key != 'src'){
	    		if($key == 'roomId'){
	    			$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
	    		}else{
	    			$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
	    		}
    	    }
    	}
    	return $st->execute() ? true : false;
    }

    public function deleteReservation(Array $data)
    {
    	# code...
    	if($data['src'] == 'app'){
    		$st = $this->_driver->prepare('CALL sp_deleteAppReservation(:roomId, :reserveId)');
    	}else{
    		$st = $this->_driver->prepare('CALL sp_deleteWebReservation(:roomId, :reserveId)');
    	}
    	$st->bindValue(':roomId', $data['roomId'], PDO::PARAM_INT);
    	$st->bindValue(':reserveId', $data['reverseId'], PDO::PARAM_STR);
    	return $st->execute() ? true : false;
    }

    public function deleteReservationFull($revId)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_deleteReservationFull(:reserveId)');
    	$st->bindValue(':reserveId', $revId, PDO::PARAM_STR);
    	return $st->execute() ? true : false;
    }

    public function addReservationPayment(Array $data)
    {
    	$st = $this->_driver->prepare('CALL sp_addReservationPayment(:date, :reserveId, :amt, :details, :src)');
    	foreach ($data as $key => $value) {
    		if($key == 'amt'){
    			$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
    		}else{
    			$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
    		}
    	}
    	$st->execute();
    	$st->bindColumn('id', $id);
    	$st->fetch(PDO::FETCH_ASSOC);
    	return $id;
    }

    public function fetchReservations($src)
    {
    	# code...
    	if($src == 'app'){
    		$st = $this->_driver->prepare('CALL sp_fetchAppReservations()');
    	}elseif($src == 'web'){
    		$st = $this->_driver->prepare('CALL sp_fetchWebReservations()');
    	}
    	return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function fetchReservedRoomsByResId($resId, $src)
    {
    	if($src == 'app'){
    		$st = $this->_driver->prepare('CALL sp_fetchAppReservationsByResId(:resId)');
    	}elseif($src == 'web'){
    		$st = $this->_driver->prepare('CALL sp_fetchWebReservationsByResId(:resId)');
    	}
    	$st->bindValue(':resId', $resId, PDO::PARAM_STR);
    	return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function fetchReservationPayments($resId)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_fetchReservationsPaymentsByResId(:resId)');
    	$st->bindValue(':resId', $resId, PDO::PARAM_STR);
    	return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();

    }

    public function fetchRoomByNo($roomNo)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_fetchRoomByNo(:roomNo)');
    	$st->bindValue(':roomNo', $roomNo, PDO::PARAM_STR);
    	return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : array();
    }



#end of class	
}