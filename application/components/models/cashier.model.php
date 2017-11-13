<?php
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class CashierModel extends BaseModel{
	
	protected $_param;
	protected $_viewParams;
	
	public function execute(Array $options){ 
		$this->_viewParams = $options;
		$this->notify();
	} 
 
	public function addBankDeposit(Array $data)
	{
		global $registry;
		

		$requiredFields = array('date', 'bank', 'amt1', 'amt2');
		# get all form fields into an array...
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}
		
		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			$this->execute(array('action'=>'display', 'tmpl' => 'bankDepositForm', 'widget' => 'error', 'msg' => $checkReq->msg));
		}

		#sanitize each of the fields & append to sanitized array
		$sanitized = array();
		foreach ($formFields as $key) {
			# code...
			if(in_array($key, array('amt1','amt2')) == true){
				$newAmt = amtToInt($_POST[$key]);
				$$key = $registry->get('form')->sanitize($newAmt, 'float');
			}else{
				$$key = $registry->get('form')->sanitize($_POST[$key], 'string');
			}
			$sanitized[$key] = $$key;

		}

		if($sanitized['amt2'] != $sanitized['amt1']){
			$this->execute(array('action'=>'display', 'tmpl' => 'bankDepositForm', 'widget' => 'error', 'msg' => 'Amount & Confirm Deposit Amounts must be the Same'));
		}

		$registry->get('db')->addBankDeposit(array(
			'date' => today(),
			'payDate' => $sanitized['date'],
			'transId' => generateTransId(),
			'bank' => $sanitized['bank'],
			'amt' => $sanitized['amt1']
			));


		$this->execute(array('action'=>'display', 'tmpl' => 'bankDepositForm', 'widget' => 'success', 'msg' => 'Bank deposit Successfully Posted'));


	}

	public function collectReturns(Array $data)
	{
		# code...
		global $registry;

		$requiredFields = array('date', 'priv', 'amt1', 'amt2', 'refNo');
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}
		
		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			$this->execute(array('action'=>'display', 'tmpl' => 'collectPosReturns', 'widget' => 'error', 'msg' => $checkReq->msg));
		}

		#sanitize each of the fields & append to sanitized array
		$sanitized = array();
		foreach ( $formFields as $key ) {
			# code...
			if ( in_array($key, array( 'amt1', 'amt2', 'staffId' )) == true ) {
				$newAmt = amtToInt($_POST[ $key ]);
				$$key = $registry->get('form')->sanitize($newAmt, 'float');
			}
			else {
				$$key = $registry->get('form')->sanitize($_POST[ $key ], 'string');
			}
			$sanitized[ $key ] = $$key;

		}

		# if cash had already been collected
		if($sanitized['amtDue'] == "Cash Already Collected"){
			$msg = 'Cash already collected for ' . User::getRole($sanitized['priv']) . ' for ' . dateToString
					($sanitized['date']);
			$this->execute(array( 'action' => 'display', 'tmpl' => 'collectPosReturns', 'widget' => 'error', 'msg' =>
					$msg ));
		}else {



			if ( $sanitized['amt1'] != $sanitized['amt2'] ) {
				$this->execute(array( 'action' => 'display', 'tmpl' => 'collectPosReturns', 'widget' => 'error', 'msg' => 'Amount Paid and Confirm Amount paid must be equal' ));
			}

			$registry->get('db')->addCashierCollection(array(
					'date'       => today(),
					'returnDate' => $sanitized[ 'date' ],
					'transId'    => generateTransId(),
					'refNo'      => $sanitized[ 'refNo' ],
					'amtPayable' => $sanitized[ 'amtDue' ],
					'amtPaid'    => $sanitized[ 'amt1' ],
					'staffId'    => $sanitized[ 'staffId' ],
				//'staffId' => 1,
					'privilege'  => $sanitized[ 'priv' ]
			));

			if ( $sanitized[ 'amtDue' ] > $sanitized[ 'amt1' ] ) {
				# if dept did not pay all wat there where xpected to pay

				$registry->get('db')->addDeptCredit(array(
						'date'      => $sanitized[ 'date' ],
						'amt'       => $sanitized[ 'amtDue' ] - $sanitized[ 'amt1' ],
						'transId'   => generateTransId(),
						'staffId'   => $sanitized[ 'staffId' ],
					//'staffId' => 1,
						'privilege' => $sanitized[ 'priv' ]
				));
			}

			$this->execute(array( 'action' => 'display', 'tmpl' => 'collectPosReturns', 'widget' => 'success', 'msg' => 'Cash Collection Successful' ));

		}

	}

	public function fetchDeptCreditPayments($transId, $date, $priv)
	{
		# code...
		global $registry;
		$msg = $registry->get('db')->fetchDeptCreditPaymentsDetails($transId, $date, $priv);

		$this->execute(array('action'=>'display', 'tmpl' => '', 'widget' => 'deptCreditPayments', 'msg' => $msg));
	}

	public function fetchDeptCreditBal($date, $priv)
	{
		# code...
		global $registry;
		$credits = $registry->get('db')->fetchDeptCredit($date, $priv);
		if(empty($credits)){
			echo json_encode(array(
				'status' => 'error',
				'errMsg' => 'Not Credit was Found for ' . User::getRole($priv) . ' for ' . dateToString($date)
				));
		}else{
				$crdt = 0;
				$transId = $credits[0]->transId;

				foreach ($credits as $row) {
					# code...
					$crdt += $row->amt;
				}

				$cPay = 0;
				foreach ($registry->get('db')->fetchDeptCreditPaymentsDetails($transId, $date, $priv) as $key) {
					# code...
					$cPay += $key->amt;
				}

				echo json_encode(array(
					'status' => 'success',
					'amt' => $crdt - $cPay,
					'transId' => $transId
					));

		}


	}


	public function addNewAsAtPaymemt(Array $data)
	{
		# code...
		global $registry;

		$requiredFields = array('creditDate', 'priv', 'amt1', 'amt2');
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}
		
		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			$this->execute(array('action'=>'display', 'tmpl' => 'newAsAtPayment', 'widget' => 'error', 'msg' => $checkReq->msg));
		}

		#sanitize each of the fields & append to sanitized array
		$sanitized = array();
		foreach ($formFields as $key) {
			# code...
			if(in_array($key, array('amt1','amt2', 'staffId')) == true){
				$newAmt = amtToInt($_POST[$key]);
				$$key = $registry->get('form')->sanitize($newAmt, 'float');
			}else{
				$$key = $registry->get('form')->sanitize($_POST[$key], 'string');
			}
			$sanitized[$key] = $$key;

		}

		if($amt1 != $amt2){
			$this->execute(array('action'=>'display', 'tmpl' => 'newAsAtPayment', 'widget' => 'error', 'msg' => 'Amount Paid and Confirm Amount paid must be equal'));
		}

		$registry->get('db')->addDeptCreditPayment(array(
			'date' => today(),
			'creditDate' => $sanitized['creditDate'],
			'transId' => $sanitized['transId'],
			'amt' => $sanitized['amt1'],
			'staffId' => $sanitized['staffId'],
			//'staffId' => 1,
			'privilege' => $sanitized['priv']
			));

		$this->execute(array('action'=>'display', 'tmpl' => 'newAsAtPayment', 'widget' => 'success', 'msg' => 'As At payment Successfully posted'));


	}

	public function setCashBookDate(Array $data)
	{
		# code...
		global $registry;

		$date = filter_var($data['date'], FILTER_SANITIZE_STRING);
		$dir = filter_var($data['direction'], FILTER_SANITIZE_NUMBER_INT);
		
		$registry->get('session')->write('cashBkDate', $date);
		$registry->get('session')->write('cashBkDir', $dir);
		
		$this->execute(array('action'=>'render', 'tmpl' => 'cashBook', 'widget' => '', 'msg' => ''));

	}



	


	#end of class
}

