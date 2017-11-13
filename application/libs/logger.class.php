<?php
namespace application\libs;
use core\libs\Logger as CoreLogger;
use application\libs\LoggerDatabase as Db;
/**
*
*
*/
defined('ACCESS') || AppError::exitApp();

/**
*
*/
class Logger extends CoreLogger
{

	function __construct(Db $db)
	{
		# code...
		parent::__construct($db);
	}

	public function logGuestCreditCheckOut($data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$guest = new \Guest($data[0]);
		$room = new \Room($data[2]);
		$thisUser = unserialize($session->read('thisUser'));

		$this->_db->logNotification(array(
								'date' => today(),
								'time' => now(),
								'notType' => 1,
								'details' => 'Checked out Guest ( ' . $guest->name .  ' ) from Room ( ' . $room->no .' ) on Credit',
								'staffId' => $thisUser->staffId
							));


	}

	public function logGuestCreditCheckIn($data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$guest = new \Guest($data['guestId']);
		$room = new \Room($data['roomId']);
		$thisUser = unserialize($session->read('thisUser'));

		$this->_db->logNotification(array(
								'date' => today(),
								'time' => now(),
								'notType' => 3,
								'details' => 'Checked In Guest ( ' . $guest->name .  ' ) to Room ( '. $room->no . ' ) on Credit',
								'staffId' => $thisUser->staffId
							));


	}


	public function logComplimentaryCheckIn(Array $params)
	{
		# code...

		global $registry;
		$session = $registry->get('session');

		$guest = new \Guest($params['guestId']);
		$room = new \Room($params['roomId']);
		$thisUser = unserialize($session->read('thisUser'));

		$this->_db->logNotification(array(
								'date' => today(),
								'time' => now(),
								'notType' => 2,
								'details' => 'Checked In Guest ( ' . $guest->name .  ' ) to Room ( '. $room->no . ' ) on Complimentary',
								'staffId' => $thisUser->staffId
							));

	}

	public function logRoomChange(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$guest = new \Guest($data['guestId']);
		$oldRoom = new \Room($data['oldRoomId']);
		$newRoom = new \Room($data['newRoomId']);

		$thisUser = unserialize($session->read('thisUser'));

		$this->_db->logNotification(array(
								'date' => today(),
								'time' => now(),
								'notType' => 4,
								'details' => 'Changed Guest ( ' . $guest->name .  ' ) Room from ( '. $oldRoom->no . ' ) to ' . $newRoom->no,
								'staffId' => $thisUser->staffId
							));
	}

	public function logDiscountChange(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$guest = new \Guest($data['guestId']);
		$room = new \Room($data['roomId']);

		$thisUser = unserialize($session->read('thisUser'));

		$this->_db->logNotification(array(
								'date' => today(),
								'time' => now(),
								'notType' => 5,
								'details' => 'Changed Guest ( ' . $guest->name .  ' ) discount from ( '. $data['oldDiscount'] . ' ) to ' . $data['newDiscount'],
								'staffId' => $thisUser->staffId
							));
	}

	public function logExpensesTransfer(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$thisUser = unserialize($session->read('thisUser'));

		$bg = new \Guest($data['bg']);
		$pg = new \Guest($data['pg']);

		$bgRoom = new \Room($data['bgRoom']);
		$pgRoom = new \Room($data['pgRoom']);

		$this->_db->logNotification(array(
								'date' => today(),
								'time' => now(),
								'notType' => 6,
								'details' => 'Transfered Guest Expenses for Guest ( ' . $bg->name . ' ) staying in Room ( ' . $bgRoom->no .' ) to Guest ( ' . $pg->name . ' ) staying in Room ( ' . $pgRoom->no . ' )',
								'staffId' => $thisUser->staffId
							));
	}

	public function logReservationCancelation(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$thisUser = unserialize($session->read('thisUser'));

		$msg = 'Cancelled Reservation of Guest ( ' . $data['guestName'] . ' ) for ';
		$msg .= count($data['rooms']) > 1 ? 'Rooms' : 'Room';
		$msg .= '( ';
		for ($i=0; $i < count($data['rooms']) ; $i++) {
			# code...
			$room = new \Room($data['rooms'][$i]);
			$msg .= $room->no . ', ';
		}
		$msg = trim($msg, ', ');
		$msg .= ' )<br />';
		$msg .= 'Booking Date : ' . dateToString($data['startDate']) . ' - ' . dateToString($data['endDate']);

		$this->_db->logNotification(array(
								'date' => today(),
								'time' => now(),
								'notType' => 7,
								'details' => $msg,
								'staffId' => $thisUser->staffId
							));
	}

	public function logBadRoomAdded(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$thisUser = unserialize($session->read('thisUser'));

		$room = new \Room($data['roomId']);
		$msg = 'Added ' . $room->no . ' to Bad Room... Reason : ' . $data['reason'];

		$this->_db->logNotification(array(
								'date' => today(),
								'time' => now(),
								'notType' => 8,
								'details' => $msg,
								'staffId' => $thisUser->staffId
							));
	}


	public function logBadRoomRemoval(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$thisUser = unserialize($session->read('thisUser'));

		$room = new \Room($data['roomId']);
		$msg = 'Removed ' . $room->no . ' from List of Bad Rooms ';

		$this->_db->logNotification(array(
								'date' => today(),
								'time' => now(),
								'notType' => 9,
								'details' => $msg,
								'staffId' => $thisUser->staffId
							));
	}

	public function logTransReversalApplication(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$thisUser = unserialize($session->read('thisUser'));

		$trans = new \Transaction($data['transId']);
		$details = json_decode($trans->details);
		$msg = 'Applied for Transaction Reversal...Transaction Details : ' . $details->desc;

		$this->_db->logNotification(array(
								'date' => today(),
								'time' => now(),
								'notType' => 10,
								'details' => $msg,
								'staffId' => $thisUser->staffId
							));
	}

	public function logTransReversalApproval(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$thisUser = unserialize($session->read('thisUser'));

		$staff = new \User(new \Staff($data['staff']));


		$msg = 'Approved Transaction Reversal for  : ' . $data['details'] . ' by ' . $staff->name . '...Application Date : ' . dateToString($data['date']);

		$this->_db->logNotification(array(
								'date' => today(),
								'time' => now(),
								'notType' => 11,
								'details' => $msg,
								'staffId' => $thisUser->staffId,
								'targetStaffId' => $staff->staffId
							));
	}


	public function logTransReversalDecline(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$thisUser = unserialize($session->read('thisUser'));

		$staff = new \User($data['staff']);


		$msg = 'Rejected Transaction Reversal for  : ' . $data->details . ' by ' . $staff->name . '...Application Date : ' . $data['date'];

		$this->_db->logNotification(array(
								'date' => today(),
								'time' => now(),
								'notType' => 20,
								'details' => $msg,
								'staffId' => $thisUser->staffId,
								'targetStaffId' => $staff->staffId
							));
	}

	public function logGuestAutoBilling()
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$thisUser = unserialize($session->read('thisUser'));

		$msg = 'Checked In Guests AutoBilled';

		$this->_db->logNotification(array(
								'date' => today(),
								'time' => time(),
								'notType' => 14,
								'details' => $msg,
								'staffId' => $thisUser->staffId
							));
	}

	public function logStockRemoval(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$thisUser = unserialize($session->read('thisUser'));

		$item = new \Item($data['itemId']);
		$msg = 'Removed ' . $data['qty'] . ' ' . $item->name . ' from Stock' ;
		$msg .= '<br />( Reason : ' . $data['reason'] . ' )';

		$this->_db->logNotification(array(
								'date' => today(),
								'time' => now(),
								'notType' => 15,
								'details' => $msg,
								'staffId' => $thisUser->staffId
							));
	}

	public function logStockIssue(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$thisUser = unserialize($session->read('thisUser'));

		$item = new \Item($data['itemId']);
		$msg =  $data['qty'] . ' ' . $item->name . ' issued to ' . $data['priv'] ;

		$this->_db->logNotification(array(
								'date' => today(),
								'time' => now(),
								'notType' => 16,
								'details' => $msg,
								'staffId' => $thisUser->staffId
							));
	}

	public function logRequisitionApplication(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$thisUser = unserialize($session->read('thisUser'));

		$item = new \Item($data['itemId']);
		$msg = 'Requisited for ' . $data['qty'] . ' ' . $item->unit . ' of ' . $item->name;

		$this->_db->logNotification(array(
								'date' => today(),
								'time' => now(),
								'notType' => 12,
								'details' => $msg,
								'staffId' => $thisUser->staffId
							));
	}


	public function logRequisitionIssue(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$thisUser = unserialize($session->read('thisUser'));

		$item = new \Item($data['itemId']);
		$msg = $data['qty'] . ' ' . $item->unit . ' of ' . $item->name . ' requisited to ' . $data['role'];

		$this->_db->logNotification(array(
								'date' => today(),
								'time' => now(),
								'notType' => 13,
								'details' => $msg,
								'staffId' => $thisUser->staffId,
								'targetStaffId' => $data['staffId']
							));
	}


	public function logPurchaserStockAddition()
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$thisUser = unserialize($session->read('thisUser'));

		$msg = 'Posted new Stock Purchase';

		$this->_db->logNotification(array(
								'date' => today(),
								'time' => now(),
								'notType' => 17,
								'details' => $msg,
								'staffId' => $thisUser->staffId
							));
	}

	public function logStockAdditionApproval($date)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$thisUser = unserialize($session->read('thisUser'));

		$msg = 'Approved Stock Purchase posted on ' . dateToString($date);

		$this->_db->logNotification(array(
								'date' => today(),
								'time' => now(),
								'notType' => 18,
								'details' => $msg,
								'staffId' => $thisUser->staffId
							));
	}

	public function logStockAdditionRejection($date)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$thisUser = unserialize($session->read('thisUser'));

		$msg = 'Rejected Stock Purchase posted on ' . dateToString($date);

		$this->_db->logNotification(array(
								'date' => today(),
								'time' => now(),
								'notType' => 19,
								'details' => $msg,
								'staffId' => $thisUser->staffId
							));
	}

	public function logBillExemptionAddition(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$thisUser = unserialize($session->read('thisUser'));

		$msg = $thisUser->name . ' exempted Guest - ' . $data['guestName'] . ' ( Room : ' . $data['roomNo'] . ' ) from AutoBill for ' . dateToString(today());

		$this->_db->logNotification(array(
								'date' => today(),
								'time' => now(),
								'notType' => 21,
								'details' => $msg,
								'staffId' => $thisUser->staffId
							));

	}


	public function logLateCheckOutAddition(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$thisUser = unserialize($session->read('thisUser'));

		$msg = $thisUser->name . ' added Guest - ' . $data['guestName'] . ' ( Room : ' . $data['roomNo'] . ' ) to Late Check Out List. Date : ' . dateToString(today() . ' Time : ' . $data['time']);

		$this->_db->logNotification(array(
								'date' => today(),
								'time' => now(),
								'notType' => 22,
								'details' => $msg,
								'staffId' => $thisUser->staffId
							));

	}


	public function logUserLogin(Array $data){

		global $registry;
		$session = $registry->get('session');


		$this->_db->logUserLogin(array(
				'date' => today(),
				'time' => now(),
				'staffId' => $data['staffId'],
				'privilege' => $data['staffPrivilege']
		));

	}




	# End of Class
}
