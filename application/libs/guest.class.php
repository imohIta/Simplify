<?php
//use application\libs\GuestDatabase as Db;
defined('ACCESS') || Error::exitApp();

/**
*
*/
class Guest extends FuniObject
{
	public $id;
	public $name;
	public $phone;
	public $addr;
	public $occu;
	public $nationality;
	public $reasonForVisit;

	private $_db = null;

	function __construct($data)
	{
		# if guest is instantiated with an array
		if(is_array($data)){

			$data = (object) $data;
			$this->id = isset($data->id) ? $data->id : '';
			$this->name = isset($data->name) ? $data->name : '';
			$this->phone = isset($data->phone) ? $data->phone : '';
			$this->addr = isset($data->addr) ? $data->addr : '';
			$this->occu  = isset($data->occu) ? $data->occu : '';
			$this->nationality = isset($data->nationality) ? $data->nationality : '';
			$this->reasonForVisit = isset($data->reasonForVisit) ? $data->reasonForVisit : '';

		}else{

			global $registry;
			$this->_db = $registry->get('guestDb');

			$data = $this->_db->fetchDetails($data);
			if(is_null($data) || $data === false){
				$this->id = '';
				$this->name = '';
				$this->phone = '';
				$this->addr = '';
				$this->occu  = '';
				$this->nationality = '';
				$this->reasonForVisit = '';
			}else{
				$this->id = $data->id;
				$this->name = $data->name;
				$this->phone = $data->phone;
				$this->addr = $data->addr;
				$this->occu  = $data->occu;
				$this->nationality = $data->nationality;
				$this->reasonForVisit = $data->reasonForVisit;
			}
		}
	}

	public function fetchBills()
	{
		# code...
		return $this->_db->fetchBills($this->id);
	}

	public function fetchTotalBills()
	{
		# code...
		return $this->_db->fetchTotalBill($this->id);
	}

	public function deleteBills()
	{
		# code...
		return $this->_db->deleteBills($this->id);
	}

	public function fetchPayments($date = '')
	{
		# code...
		return $this->_db->fetchPayments($this->id, $date);
	}

	public function fetchPaymentsForDate()
	{
		# code...
		return $this->_db->fetchTotalGuestPaymentsForDate($this->id);
	}

	public function fetchTotalPayments()
	{
		# code...
		return $this->_db->fetchTotalPayment($this->id);
	}

	public function fetchTotalPaymentsForDate($date)
	{
		# code...
		return $this->_db->fetchTotalPaymentsForDate($this->id, $date);
	}

	public function deletePayments()
	{
		# code...
		return $this->_db->deletePayments($this->id);
	}

	public function getCheckInInfo2()
	{
		# code...
		return $this->_db->getCheckInInfo2($this->id);
	}




	/****************************
		STATIC FUNCTIONS
		*************************/

	public static function fetchDetailsByPhone($phone)
	{
		# code...
		global $registry;
		$db = $registry->get('guestDb');
		return $db->fetchDetailsByPhone($phone);
	}

	public static function deleteFromRegister($guestId, $roomId)
	{
		# code...
		global $registry;
		$db = $registry->get('guestDb');
		return $db->deleteFromRegister($guestId, $roomId);
	}

	public static function getOutstandingBal($phone)
	{
		global $registry;
		$db = $registry->get('guestDb');
		return $db->getOutstandingBal($phone);
	}

	public static function addOutstandingBal(Array $values)
	{
		global $registry;
		$db = $registry->get('guestDb');
		return $db->addOutstandingBal($values);
	}

	public static function addCredit(Array $values)
	{
		global $registry;
		$db = $registry->get('guestDb');
		return $db->addCredit($values);
	}

	public static function addCreditPayment(Array $values)
	{
		global $registry;
		$db = $registry->get('guestDb');
		return $db->addCreditPayment($values);
	}



	public static function checkIn(Array $values, $compli = false, $flatRate = false)
	{
		# code...

		global $registry;
		$db = $registry->get('guestDb');
		$db->checkIn($values, $compli, $flatRate);
	}

	public static function checkInFromReservation(Array $values)
	{
		# code...
		global $registry;
		$db = $registry->get('guestDb');
		$db->checkInFromReservation($values);
	}

	public static function getCheckInInfo($roomId)
	{
		# code...
		global $registry;
		$db = $registry->get('guestDb');
		return $db->getCheckInInfo($roomId);
	}

	public static function addBill(Array $values)
	{
		# code...
		global $registry;
		$db = $registry->get('guestDb');
		return $db->addBill($values);
	}


	public static function addPayment(Array $values)
	{
		# code...
		global $registry;
		$db = $registry->get('guestDb');


		# create new Guest
		$guest = new Guest($values['guestId']);

		# fetch the guest current room No
		$data = $guest->getCheckInInfo2();

		#append roomNo to values

		$values['roomId'] = $data->roomId;
		return $db->addPayment($values);
	}

	public static function addRefund(Array $values)
	{
		# code...
		global $registry;
		$db = $registry->get('guestDb');
		return $db->addRefund($values);
	}

	public static function changeIdInBillsTbl(Array $values)
	{
		# code...
		global $registry;
		$db = $registry->get('guestDb');
		return $db->changeIdInBillsTbl($values);
	}

	public static function addStayActivity(Array $values)
	{
		# code...
		global $registry;
		$db = $registry->get('guestDb');
		return $db->addStayActivity($values);
	}

	public static function addAsBillPayer(Array $values)
	{
		# code...
		global $registry;
		$db = $registry->get('guestDb');
		return $db->addAsBillPayer($values);
	}

	public static function deleteFromBillPayers($guestId, $roomId)
	{
		# code...
		global $registry;
		$db = $registry->get('guestDb');
		return $db->deleteFromBillPayers($guestId, $roomId);
	}

	public static function updateRoomInRegister($params)
	{
		# code...
		global $registry;
		$db = $registry->get('guestDb');
		return $db->updateRoomInRegister($params);
	}

	public static function updateBills(Array $params)
	{
		# code...
		global $registry;
		$db = $registry->get('guestDb');
		return $db->updateBills($params);
	}

	public static function updateDiscount(Array $params)
	{
		# code...
		global $registry;
		$db = $registry->get('guestDb');
		return $db->updateDiscount($params);
	}

	public static function fetchPreviousGuestDetails($guestPhone)
	{
		# code...
		global $registry;
		$db = $registry->get('guestDb');
		return $db->fetchPreviousGuestDetails($guestPhone);
	}

	public static function fetchNameByPhone($guestPhone)
	{
		# code...
		global $registry;
		$db = $registry->get('guestDb');
		return $db->fetchNameByPhone($guestPhone);
	}

	public static function fetchPreviousGuestCredits($guestPhone)
	{
		# code...
		global $registry;
		$db = $registry->get('guestDb');
		return $db->fetchPreviousGuestCredits($guestPhone);
	}

	public static function fetchPreviousGuestPayments($guestPhone)
	{
		# code...
		global $registry;
		$db = $registry->get('guestDb');
		return $db->fetchPreviousGuestPayments($guestPhone);
	}

	public static function fetchTotalPreviousGuestCredits($guestPhone)
	{
		# code...
		global $registry;
		$db = $registry->get('guestDb');
		return $db->fetchTotalPreviousGuestCredits($guestPhone);
	}

	public static function fetchTotalPreviousGuestPayments($guestPhone)
	{
		# code...
		global $registry;
		$db = $registry->get('guestDb');
		return $db->fetchTotalPreviousGuestPayments($guestPhone);
	}

	public static function autoBill()
	{
		# code...

		global $registry;
		$db = $registry->get('guestDb');

    	$autoBillSuccessfull = false;

		# also check if the current time is greater than 1pm and less than 3pm
		//if (time() >= strtotime("1pm")) {

			# loop tru guest register
			foreach ( Room::getOccupied() as $row ) {



				# if autobill time has reached for this room

				if ( time() >= $row->autoBillTime ) { //

					# check is guests has not already been autobilled today
					//if ( !$db->autoBillAlreadyExecuted(today()) ) {

						# check if guest is not in bill exemption list for today
						if ( !Room::isExemptedFromAutoBilling($row->roomId, today()) ) {

							$room = new Room($row->roomId);

							# check if room is routine check in
							if(strtolower($room->checkInType) == "routine") {
								$discountValue = ($row->discount / 100) * $room->price;
								$bill = $room->price - $discountValue;

								$amt = Room::inLateCheckOutList($room->id, today()) ? $bill / 2 : $bill;


								/***
								 * To get the loogged in receptionist
								 * all user loggin must be captured
								 * so that the checkin receptionist can be fetched
								 */
								# get the checked in reception User
								$loggedInReceptionist = $registry->get('db')->getCheckInReceptionist();
								if(is_null($loggedInReceptionist) || $loggedInReceptionist === false){
									//$loggedInReceptionist = $registry->get('db')->fetchAnyReceptionist();
									$loggedInReceptionist = 0;
								}

								# auto Bill Guest
								$db->addBill(array(
										'date' => today(), 'guestId' => $row->guestId, 'roomId' => $room->id, 'transId' => generateTransId(), 'amt' => $amt, 'billType' => 2, 'details' => 'Room Charge ( Auto )', 'staffId' => $loggedInReceptionist
								));
							}

							# update Auto bill time for this Guest
							$db->updateAutoBillTime(array(
									'id'           => $row->id,
									'autoBillTime' => autoBillTime()
							));

							if ( !$autoBillSuccessfull ) {
								$autoBillSuccessfull = true;
							}
						}

						# fetch all guest and the rooms they currently occupy and store them
						$mapping = array();
						foreach ( Room::getOccupied() as $r ) {
							$mapping[] = array( 'guestId' => $r->guestId, 'roomId' => $r->roomId );
						}
						$registry->get('guestDb')->storeGuestMapping(array(
								'date'     => today(),
								'mappings' => json_encode($mapping)
						));

					//}

				}

			}

			if ( $autoBillSuccessfull ) {
				# log
				$registry->get('logger')->logGuestAutoBilling();
			}
			return true;
		//}else{
		//	return false;
		//}
	}

	public static function fetchDistinctDebtors()
	{
		# code...
		global $registry;
		return $registry->get('guestDb')->fetchDistinctDebtors();
	}

	public static function fetchBalances(){
		global $registry;
		return $registry->get('guestDb')->fetchGuestBalances();
	}




#end of class
}
