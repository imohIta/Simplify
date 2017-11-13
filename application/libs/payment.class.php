<?php
use application\libs\PaymentDatabase as Db;
defined('ACCESS') || Error::exitApp();

/**
* 
*/
class Payment extends FuniObject
{
	public $id;
	public $date;
	public $guestId;
	public $transId;
	public $amt;
	public $details;

	private $_db = null;

	function __construct($paymentId)
	{ 
		# code... 
		global $registry;
		$this->_db = $registry->get('payDb');

		$data = $this->_db->getPaymentById($paymentId);

		if(is_null($data) or empty($data)){
			$this->id = null;
			$this->date = null;
			$this->guestId = null;
			$this->roomId  = null;
			$this->transId = null;
			$this->type = null;
			$this->details = null;
		}else{
			$this->id = $id;
			$this->date = $data->date;
			$this->guestId = $data->guestId;
			$this->roomId  = $data->roomId;
			$this->transId = $data->transId;
			$this->type = $data->type;
			$this->details = $data->details;
		}
	}


	/**************************************
			STATIC FUNCTIONS
	**************************************/

	public static function fetchDistinctGuestPayments($date)
	{
		# code...
		global $registry;
		$db = $registry->get('payDb');
		return $db->fetchDistinctGuestPayments($date);
	}

	public static function delete($transId)
	{
		# code...
		global $registry;
		$db = $registry->get('payDb');
		return $db->delete($transId);
	}

	
	


#end of class
}



