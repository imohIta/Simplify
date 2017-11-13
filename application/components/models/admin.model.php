<?php
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class AdminModel extends BaseModel{

	protected $_param;
	protected $_viewParams;

	private $resetHash = '$2y$10$rQ5iENJOXSQhXTFu5BpjoucFEKm9We8wlECaP0H.bn4syITWNElZG';
	private $resetPwd = 'SYz@A+min0_0';

	public function execute(Array $options){
		$this->_viewParams = $options;
		$this->notify();

		# SYz@A+min0_0
	}

	public function resetApp(Array $data)
	{
		# code...
		global $registry;

		$session = $registry->get('session');

		$requiredFields = array('pwd', 'pwd2');
		# get all form fields into an array...
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}

		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			$this->execute(array('action'=>'display', 'tmpl' => 'resetApp', 'widget' => 'error', 'msg' => $checkReq->msg));
		}

		#sanitize each of the fields & append to sanitized array
		$sanitized = array();
		foreach ($formFields as $key) {
			# code...
			$$key = $registry->get('form')->sanitize($_POST[$key], 'string');

			$sanitized[$key] = $$key;

		}

		if(strtolower($sanitized['pwd']) != strtolower($sanitized['pwd2'])){
			# if password and confirm password r not the same
			$this->execute(array('action'=>'display', 'tmpl' => 'resetApp', 'widget' => 'error', 'msg' => 'Reset Password & Confirm Reset Password must be the Same'));

		}


		# check if pwd matches reset pwd
		//if(!$registry->get('authenticator')->verifyPassword2($sanitized['pwd'], $this->resetPwd)){
		if($sanitized['pwd'] != $this->resetPwd){
			$this->execute(array('action'=>'display', 'tmpl' => 'resetApp', 'widget' => 'error', 'msg' => 'Sorry Dude...You cant do this...Your password was incorrect'));

		}else{

			$truncateTbls = array('appReservations', 'badRooms', 'bankDeposits', 'billPayers', 'cashierCollections', 'chairmanXpenses', 'closingStock', 'creditPayments', 'credits', 'deptCreditPayments', 'deptCredits', 'guestActivityArchives', 'guestRegister', 'guestBalances', 'guestBills', 'guestCreditPayments', 'guestPayments', 'guestCredits', 'guestRefunds', 'impressAcct', 'impressCategories', 'impressExpenditures', 'impressPayIns', 'impressTrend', 'notifications', 'requisitions', 'reservationPayments', 'sales', 'staffCreditPayments', 'staffCredits', 'stockItemRemovals', 'stockPurchases', 'transactionReversals', 'transactions', 'webReservations');

			$updateTables = array('house_keepingStk', 'kitchenStk', 'main_barStk', 'pool_barStk', 'resturantStk', 'resturant_drinksStk', 'store' );

			foreach ($truncateTbls as $key => $value) {
				# code...
				$registry->get('db')->truncateTbl($value);
			}

			foreach ($updateTables as $key => $value) {
				# code...
				$registry->get('db')->updateTbl($value);
			}

			$this->execute(array('action'=>'display', 'tmpl' => 'resetApp', 'widget' => 'success', 'msg' => 'App Reset successfull'));
		}


	}

	public function flushTable(Array $data)
	{
		# code...
		global $registry;

		$pwd = filter_var($data['pwd'], FILTER_SANITIZE_STRING);
		$pwd2 = filter_var($data['pwd2'], FILTER_SANITIZE_STRING);

		if($pwd != $pwd2){
			$this->execute(array('action'=>'display', 'tmpl' => 'flushTable', 'widget' => 'error', 'msg' => 'Reset Password & Confirm Reset Password must be the Same'));
		}

		# check if pwd matches reset pwd
		if(!$registry->get('authenticator')->verifyPassword2($pwd, $this->resetHash)){
			$this->execute(array('action'=>'display', 'tmpl' => 'flushTable', 'widget' => 'error', 'msg' => 'Sorry Dude...You cant do this...Your password was incorrect'));
		}else{

		foreach ($data['tables'] as $key => $value) {
			# code...
			$tbl = filter_var($value, FILTER_SANITIZE_STRING);
			$registry->get('db')->truncateTbl($tbl);
		}

		$this->execute(array('action'=>'display', 'tmpl' => 'flushTable', 'widget' => 'success', 'msg' => 'App Reset successfull'));
	   }
	}


	public function setShiftTimes()
	{
		# try to update shift times
		setShiftTimes();
	}






	#end of class
}
