<?php
use application\libs\TransactionDatabase as Db;
defined('ACCESS') || Error::exitApp();

/**
*
*/
class Transaction extends FuniObject
{
	public $id;
	//public $autoId;
	//public $transId;
	public $type;
	public $date;
	public $time;
	protected $_dbId; # Auto increment Id in the Database
	public $src;
	public $details;
	public $desc;
	public $staffId;
	public $privilege;

	private $_db = null;

	function __construct($transId)
	{
		# code...
		global $registry;
		$this->_db = $registry->get('transDb');

		$data = $this->_db->getTransactionById($transId);
		if($data === false || is_null($data)){
			// if transaction cannot be found using trans id
			// try finding it using auo increment id
			$data = $this->_db->getTransactionByAutoId($transId);

			if(!$data === false && !is_null($data)){
				$this->id = $data->transId;
				$this->type = $data->transType;
				$this->date = $data->date;
				$this->time = $data->time;
				$this->_dbId  = $data->id;
				$this->src = $data->src;
				$this->details = $data->details;
				$this->desc = $this->_db->fetchDesc($this->type);
				$this->staffId = $data->staffId;
				$this->privilege = $data->privilege;
			}else{ # if transaction is still not found
				$this->id = null;
				$this->type = null;
				$this->date = null;
				$this->time = null;
				$this->_dbId  = null;
				$this->src = null;
				$this->details = null;
				$this->desc = null;
				$this->staffId = null;
				$this->privilege = null;
			}
		}else{
			$this->id = $transId;
			$this->type = $data->transType;
			$this->date = $data->date;
			$this->time = $data->time;
			$this->_dbId  = $data->id;
			$this->src = $data->src;
			$this->details = $data->details;
			$this->desc = $this->_db->fetchDesc($this->type);
			$this->staffId = $data->staffId;
			$this->privilege = $data->privilege;
		}
	}

	public function extractSrc($toArray = false)
	{
		# code...
		return ($toArray) ? json_decode($this->src, true) : json_decode($this->src);
	}

	public function extractDetails($toArray = false)
	{
		# code...
		return ($toArray) ? json_decode($this->details, true) : json_decode($this->details);
	}

	public function fetchSrcDetails()
	{
		# code...
		$src = $this->extractSrc();
		return $this->_db->fetchSrcDetails($src->tbl, $src->id);
	}


	/*
	* Reverse a transaction
	* Roll back all Changes on affected tables
	*/
	public function reverse($value='')
	{
		global $registry;

		# code...
		switch ($this->type) {
			case 1: # Guest Bill
					Bill::delete($this->id);
				break;

			case 2: # Guest payment
					Payment::delete($this->id);
				break;

			case 3: # Cash Sale
					# return Sales Stock
					$this->_returnSalesStock();

					# delete sales
					$registry->get('db')->deleteFromTableByTransId('sales', $this->id);
				break;

			case 4: # credit Sale
					# return Sales Stock
					$this->_returnSalesStock();

					# delete from credits tb;
					$registry->get('db')->deleteFromTableByTransId('credits', $this->id);

					# delete sales
					$registry->get('db')->deleteFromTableByTransId('sales', $this->id);
				break;

			case 7: # Guest refund
					# delete from refunds
					$registry->get('db')->deleteFromTableByTransId('guestRefunds', $this->id);
				break;

			case 8: # Guest Credits
					$registry->get('db')->deleteFromTableByTransId('guestCredits', $this->id);
				break;

			case 9: # Guest Credit Payment
					$registry->get('db')->deleteFromTableByTransId('guestCreditPayments', $this->id);
				break;

			case 10: # Reservation Payment
					$registry->get('db')->deleteFromTableByTransId('reservationPayments', $this->id);
				break;

			case 11: # Chairman Expenses
					$registry->get('db')->deleteFromTableByTransId('chairmanXpenses', $this->id);
				break;

			case 12: # Credit Payment
					$registry->get('db')->deleteFromTableByTransId('creditPayments', $this->id);
				break;

			case 13: # Bank Deposit
					$registry->get('db')->deleteFromTableByTransId('bankDeposits', $this->id);
				break;

			case 14: # Cashier Collections
					$registry->get('db')->deleteFromTableByTransId('cashierCollections', $this->id);
					$registry->get('db')->deleteFromTableByTransId('deptCredits', $this->id);
				break;

			case 16: # Dept Credit Payment
					$registry->get('db')->deleteFromTableByTransId('deptCreditPayments', $this->id);
				break;



			default:
				# code...
				break;
		}

		$this->_db->delete($this->id);
	}


	/*
	* Update changes made to transaction details
	* may need to make it update all other instance variable of the object is need be
	*/
	public function update($newDetails){

		global $registry;
		//$nDetails = is_array($newDetails) ? json_encode($newDetails) : $newDetails;
		//var_dump($newDetails['details']); die;
		$this->details = $newDetails['details'];
		$this->_db->updateDetails(array(
						'transId' => $this->id,
						'details' => $newDetails['details']
						));
		return true;
	}


	private function _returnSalesStock()
	{
		# fetch sales using transId
		global $registry;

		foreach ($registry->get('db')->fetchSalesByTransId($this->id) as $s) {

			# if sale was posted
			if($s->posted == 1){

				if($s->object == 1){ # if menu

					$menu = new Menu($s->objectId);
					$reductions = json_decode($menu->reductions, true);

					# if the qty demanded fro the item is less than the qty in stock
					foreach($reductions as $k => $v) {
						$item = new PosItem(new Item($k), 'kitchenStk');
						$item->IncreaseStockQty($v * $value['qty']);
					}

				}else{

					$user = new User($s->staffId);

					$item = new PosItem(new Item($s->objectId), User::getTblByPrivilege($s->privilege));
					$item->IncreaseStockQty($s->qty);
				}
			}
		}
	}


	/****************************
		STATIC FUNCTIONS
		*************************/


	public static function addNew(Array $values)
	{
		# code...
		global $registry;
		$db = $registry->get('transDb');
		return $db->addNew($values);
	}

	public static function updateDetails(Array $values)
	{
		# code...
		global $registry;
		$db = $registry->get('transDb');
		return $db->updateDetails($values);
	}

	public static function fetchUserTransactions($userId, $priv, $date)
	{

		# code...
		global $registry;
		$db = $registry->get('transDb');
		$session = $registry->get('session');

		$shiftTimes = $registry->get('db')->fetchShiftTimes($date);

		$beginTime = (is_null($shiftTimes) || false === $shiftTimes) ? $session->read('shiftBeginTime') : $shiftTimes->beginTime;
		$endTime = (is_null($shiftTimes) || false === $shiftTimes) ? $session->read('shiftEndTime') :
				$shiftTimes->endTime;


		return $db->fetchUserTransactions($userId, $priv, $date, $beginTime, $endTime);
	}


	public static function applyForReversal(Array $data)
	{
		# code...
		global $registry;
		$db = $registry->get('transDb');
		return $db->applyForReversal($data);
	}

	public static function query(Array $data)
	{
		# code...
		global $registry;
		$db = $registry->get('transDb');
		return $db->executeQuery($data);
	}

	/*
	* fetch a particular transaction type
	*/
	public static function fetch($type, $date='')
	{
		# code...
		global $registry;
		return $registry->get('transDb')->fetch($type, $date);
	}

	public static function fetchForFinancialReport($month, $year)
	{
		# code...
		global $registry;

		$year = filter_var($year, FILTER_SANITIZE_NUMBER_INT);
		$month = filter_var($month, FILTER_SANITIZE_NUMBER_INT);

		$beginDate = $year . '-' . $month . '-01';
		$endDate = $year . '-' . $month . '-' . getMonthLastDate($month);

		return $registry->get('transDb')->fetchForFinancialReport($beginDate, $endDate);
	}

	public static function fetchReversals($limit)
	{
		# code...
		global $registry;
		return $registry->get('transDb')->fetchReversals($limit);
	}

	public static function fetchReversalById($id)
	{
		# code...
		global $registry;
		return $registry->get('transDb')->fetchReversalById($id);
	}

	public static function updateReversalInfo($reversalInfo, $status, $id)
	{
		# code...
		global $registry;
		return $registry->get('transDb')->updateReversalInfo($reversalInfo, $status, $id);
	}

	public static function fetchDesc($type)
	{
		# code...
		global $registry;
		return $registry->get('transDb')->fetchDesc($type);
	}




#end of class
}
