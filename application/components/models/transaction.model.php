<?php
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class TransactionModel extends BaseModel{

	protected $_param;
	protected $_viewParams;

	public function execute(Array $options){
		$this->_viewParams = $options;
		$this->notify();
	}

	public function showReversalForm()
	{
		# code...
		$this->execute(array('action'=>'render', 'tmpl' => 'reverseTransaction', 'widget' => '', 'msg' => ''));
	}

	public function reverseAppli(Array $data)
	{
		# code...

		global $registry;

		$requiredFields = array('transId', 'reason');
		# get all form fields into an array...
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}

		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			$this->execute(array('action'=>'display', 'tmpl' => 'reverseTransaction', 'widget' => 'error', 'msg' => $checkReq->msg));
		}

		#sanitize each of the fields & append to sanitized array
		$sanitized = array();
		foreach ($formFields as $key) {
			# code...
			$$key = $registry->get('form')->sanitize($_POST[$key], 'string');
			$sanitized[$key] = $$key;

		}

		if(!isset($_POST['found']) || $sanitized['found'] != 'yes'){
			$this->execute(array('action'=>'display', 'tmpl' => 'reverseTransaction', 'widget' => 'error', 'msg' => 'Please seach for Transaction first before submiting reversal form'));
		}

		$trans = new Transaction(trim($sanitized['transId']));
		$reversalInfo = json_encode(array(
				'reversalAppliDate' => today(),
				'reversalAppliReason' => $sanitized['reason']
				));
		$transInfo = json_encode(array(
				'transDate' => $trans->date,
				'transType' => $trans->type,
				'staffId' => $trans->staffId,
				'privilege' => $trans->privilege
				));

		Transaction::applyForReversal(array(
			'transId' => $trans->id,
			'reversalInfo' => $reversalInfo,
			'transInfo' => $transInfo,
			'details' => $trans->details,
			'status' => 0
			));

		# log transaction reversal application
		$registry->get('logger')->logTransReversalApplication(array(
				'transId' => $trans->id
				));

		$this->execute(array('action'=>'display', 'tmpl' => 'reverseTransaction', 'widget' => 'success', 'msg' => 'Transaction reversal Application successfully sent'));

	}

	public function getTransById($transId)
	{
		# code...
		global $registry;
		$thisUser = unserialize($registry->get('session')->read('thisUser'));

		$trans = new Transaction($transId);
		if(is_null($trans->id)){

			$this->execute(array('action'=>'display', 'tmpl' => '', 'widget' => 'error', 'msg' => 'Transaction not found'));

		}elseif(!$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4)) && ($thisUser->id != $trans->staffId || $thisUser->get('activeAcct') != $trans->privilege)){

			$this->execute(array('action'=>'display', 'tmpl' => '', 'widget' => 'error', 'msg' => 'You cannot view this Transaction'));

		}else{

			$msg['trans'] = $trans;
			$this->execute(array('action'=>'display', 'tmpl' => '', 'widget' => 'transInfo', 'msg' => $msg));
		}
	}

	public function showTransDetails($id)
	{
		# code...
		$tId = filter_var($id, FILTER_SANITIZE_STRING);
		$data = Transaction::fetchReversalById($id);
		$this->execute(array('action'=>'display', 'tmpl' => '', 'widget' => 'viewTransDetails', 'msg' => $data));
	}

	public function showTransDetails2($transId)
	{
		# code...
		echo 'Transaction Details Under Development';
		/*$tId = filter_var($transId, FILTER_SANITIZE_STRING);
		$this->execute(array('action'=>'display', 'tmpl' => '', 'widget' => 'viewTransDetails2', 'msg' => $transId));*/
	}

	public function showLog($data = array())
	{
		# code...
		global $registry;
		$session = $registry->get('session');
		$thisUser = unserialize($session->read('thisUser'));

		$date = empty($data) ? today() : filter_var($data['date']);
		$registry->get('session')->write('transDate', $date);

		# Render diffrent show transaction to mgt staff

		$acctLogToShow = $session->read('logPrivilege' . $thisUser->id . $thisUser->get('activeAcct'))
						? $session->read('logPrivilege' . $thisUser->id . $thisUser->get('activeAcct'))
						: $thisUser->get('activeAcct');

		if($registry->get('authenticator')->checkPrivilege($acctLogToShow, array(6))){
			# Cashier

			$tmpl = 'cashierTransactions';

			# Reception
		}elseif($registry->get('authenticator')->checkPrivilege($acctLogToShow, array(7))){

			$tmpl = 'receptionTransactions';

			# Main Bar, Pool bar, Resturant & Resturant Drinks
		}elseif($registry->get('authenticator')->checkPrivilege($acctLogToShow, array(8,9,10,11))){

			# point of Sale transaction
			$tmpl = 'posTransactions';

		}else{ # other Account...split further if possible
			$tmpl = 'OthersTransactions';
		}

		$this->execute(array('action'=>'render', 'tmpl' => $tmpl, 'widget' => '', 'msg' => ''));
	}

	public function calculateReturns($priv, $date)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$date = filter_var($date, FILTER_SANITIZE_STRING);
		$priv = filter_var($priv, FILTER_SANITIZE_NUMBER_INT);

		#check if cash has already been collected fro this date for this dept
		if($registry->get('db')->checkCashReturnsCollected($date, $priv)){
			echo "Cash Already Collected";


		}else {


			# fetch the shift times
			$shiftTimes = $registry->get('db')->fetchShiftTimes($date);

			// i added this to make cashier return fetch using time
			// i.e btw 8pm of that day to 8am of the next day
			$startTime = (is_null($shiftTimes) || false === $shiftTimes) ? strtotime("yesterday 8am") : $shiftTimes->beginTime;
			$endTime = (is_null($shiftTimes) || false === $shiftTimes) ? strtotime("today 8am") : $shiftTimes->endTime;

			switch ( $priv ) {
				case 7: # reception

					# select guest & reservation payment
					$query = 'select * from `transactions` where `privilege` = :privilege and `transType` in (2,10,17) and (`time` between :startTime and :endTime)';
					break;

				default: # POS Units

					# select cash and credit sales
					//$query = 'select * from `transactions` where `date` = :date and `privilege` = :privilege and `transType` in (3,4)';
					$query = 'select * from `transactions` where `privilege` = :privilege and `transType` in (3,4,17) and (`time` between :startTime and :endTime)';
					break;
			}

			$collections = Transaction::query(array(
					'query'  => $query,
					'values' => array(
							'privilege' => $priv,
							'startTime' => $startTime,
							'endTime'   => $endTime

					)
			));

			//var_dump($collections);

			$collectionsSum = 0;
			foreach ( $collections as $row ) {
				# code...
				$t = new Transaction($row->transId);
				$det = json_decode($t->details);

				//if reception
				if ( $priv == 7 ) {
					$d = json_decode($det->desc, true);
					if ( strtolower($d[ 'Pay Type' ]) == 'cash' ) {
						$collectionsSum += $det->amt;
					}
				}
				else {
					//$d = json_decode($det->desc, true);

					if ( strtolower($det->type) == 'cash sale' ) {
						$collectionsSum += $det->amt;
					}
				}
			}
			//echo $collectionsSum; return;


			if ( $priv == 7 ) {

				# if reception ... remove guest Refund 
				$query = 'select * from `transactions` where `date` = :date and `privilege` = :privilege and `transType` in (7)';
				$cashOut = Transaction::query(array(
						'query'  => $query,
						'values' => array(
								'date'      => $date,
								'privilege' => $priv
						)
				));

				$cashOutSum = 0;
				foreach ( $cashOut as $row ) {
					# code...
					$t = new Transaction($row->transId);
					$det = json_decode($t->details);
					$d = json_decode($det->desc, true);
					if ( $d[ "Pay Type" ] == 'Cash' ) {
						$cashOutSum += $det->amt;
					}

				}
			}

			echo ($priv == 7) ? $collectionsSum - $cashOutSum : $collectionsSum;
		}
	}


	/*
	* This function is used by mgt staff
	* it allow the mgt staff to be able to view other depts transactions
	* by setting thier privilege in a session

	* This session is label after the mgt staff User and his privilege so it does not conflict with other accounts
	* incase of privilege change
	*/
	public function setLogPrivilege($priv)
	{
		# code...
		global $registry;
		$session = $registry->get('session');
		$uri = $registry->get('uri');
		$thisUser = unserialize($session->read('thisUser'));

		$privilege = filter_var($priv, FILTER_SANITIZE_NUMBER_INT);
		$session->write('logPrivilege' . $thisUser->id . $thisUser->get('activeAcct'), $privilege);

		$uri->redirect($registry->get('config')->get('baseUri') . '/transaction/log');
	}

	public function reverse(Array $data)
	{
		global $registry;
		$session = $registry->get('session');
		$thisUser = unserialize($session->read('thisUser'));

		$id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
		$transId = filter_var($_POST['transId'], FILTER_SANITIZE_STRING);

		$trans = new Transaction($transId);



		# fetch from trans reversal table
		$rev = Transaction::fetchReversalById($id);

		$reversalInfo = json_decode($rev->reversalInfo, true);

		$reversalInfo['attendedBy'] = $thisUser->name;
		$reversalInfo['attendedDate'] = today();

		$transDetails = json_decode($rev->details);


		# reverse transaction
		$trans->reverse();

		# update reversal info & status ... set status to approved
		Transaction::updateReversalInfo(json_encode($reversalInfo), 1, $id);

		# log transaction reversal
		$registry->get('logger')->logTransReversalApproval(array(
					'details' => $transDetails->desc,
					'staff' => $trans->staffId,
					'date' => $reversalInfo['reversalAppliDate']
					));

		$msg = '<div class="alert alert-success alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  <strong>Success!</strong>Transaction successfully Reversed</div>';

		$session->write('formMsg', $msg);
		$registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/transaction/reversals');

	}

	public function rejectReserval(Array $data)
	{
		# code...
		global $registry;
		$thisUser = unserialize($registry->get('session')->read('thisUser'));

		$id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
		$transId = filter_var($_POST['transId'], FILTER_SANITIZE_STRING);

		$trans = new Transaction($transId);

		# fetch from trans reversal table
		$rev = Transaction::fetchReversalById($id);

		$reversalInfo = json_decode($rev->reversalInfo, true);
		$reversalInfo['attendedBy'] = $thisUser->name;
		$reversalInfo['attendedDate'] = today();

		# update reversal info & status ... set status to rejected
		//Transaction::updateReversalInfo(json_encode($reversalInfo), 2, $id);

		$transDetails = json_encode($rev->details);

		//var_dump($transDetails, $transDetails->type); die;

		$registry->get('logger')->logTransReversalDecline(array(
					'details' => $transDetails->type,
					'staff' => $trans->staffId,
					'date' => $reversalInfo->reversalAppliDate
					));

		$msg = '<div class="alert alert-success alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  <strong>Success!</strong>Transaction successfully Rejected</div>';

		$session->write('formMsg', $msg);
		$registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/transaction/reversals');

	}






	#end of class
}
