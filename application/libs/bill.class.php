<?php
//use application\libs\BillDatabase as Db;
defined('ACCESS') || Error::exitApp();

/**
* 
*/ 
class Bill extends FuniObject
{
	public $id;
	public $date;
	public $guestId;
	public $roomId; 
	public $transId;
	public $type;
	public $details;
	public $amt;

	private $_db = null;

	function __construct($billId)
	{
		# code...
		global $registry;
		$this->_db = $registry->get('billsDb');

		$data = $this->_db->getBillById($billId);

		if(is_null($data)){
			Error::throwException("Bill Not Found");
		}
		$this->id = $id;
		$this->date = $data->date;
		$this->guestId = $data->guestId;
		$this->roomId  = $data->roomId;
		$this->transId = $data->transId;
		$this->type = $data->type;
		$this->amt = $data->amt;
		$this->details = $data->details;
	}


	/****************************
		STATIC FUNCTIONS
		*************************/

	public static function insertBillPayer(Array $values)
	{
		# code...
		global $registry;
		$db = $registry->get('billsDb');
		$db->insertBillPayer($values);
	}


	public static function fetchByRoomId($roomId)
	{
		# code...
		global $registry;
		$db = $registry->get('billsDb');
		return $db->fetchByRoomId($roomId);
	}

	public static function selectBillPayerRooms($guestId, $roomId='')
	{
		# code...
		global $registry;
		$db = $registry->get('billsDb');
		return $db->selectBillPayerRooms($guestId, $roomId);
	}

	public static function selectBillPayerOtherRooms($guestId, $roomId)
	{
		# code...
		global $registry;
		$db = $registry->get('billsDb');
		return $db->selectBillPayerOtherRooms($guestId, $roomId);
	}


	public static function selectBillPayersForRoom($roomId)
	{
		# code...
		global $registry;
		$db = $registry->get('billsDb');
		return $db->selectBillPayersForRoom($roomId);
	}

	public static function updateBillPayer($guestId, $roomId, Array $billTypes)
	{
		# code...
		global $registry;
		$db = $registry->get('billsDb');
		return $db->updateBillPayer($guestId, $roomId, $billTypes);
	}

	public static function updateBillPayer2($guestId, $roomId, $billTypes)
	{
		# code...
		global $registry;
		$db = $registry->get('billsDb');
		return $db->updateBillPayer2($guestId, $roomId, $billTypes);
	}

	public static function getBillsCoveredByGuestForRoom($guestId, $roomId)
	{
		# code...
		global $registry;
		$db = $registry->get('billsDb');
		return $db->getBillsCoveredByGuestForRoom($guestId, $roomId);
	}

	public static function changeBillPayerRoom($params)
	{
		# code...
		global $registry;
		$db = $registry->get('billsDb');
		return $db->changeBillPayerRoom($params);
	}

	public static function changeGuestBillsRoomId(Array $data)
	{
		# code...
		global $registry;
		$db = $registry->get('billsDb');
		return $db->changeGuestBillsRoomId($data);
	}

	public static function delete($transId)
	{
		# code...
		global $registry;
		$db = $registry->get('billsDb');
		return $db->delete($transId);
	}

	


#end of class
}



