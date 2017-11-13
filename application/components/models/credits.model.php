<?php
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class CreditsModel extends BaseModel{

	protected $_param;
	protected $_viewParams;

	public function execute(Array $options = array('action'=>'render', 'tmpl' => 'creditsLog', 'widget' => '', 'msg' => '')){
		$this->_viewParams = $options;
		$this->notify();
	}


	public function addCreditPayment(Array $data)
	{
		# code...
		//var_dump($data); die;

		global $registry;
		$thisUser = unserialize($registry->get('session')->read('thisUser'));

		# check required fields
		$required = array('date', 'transId', 'amt1', 'amt2', 'payType');

		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}

		$checkReq = json_decode($registry->get('form')->checkRequiredFields($required));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			$this->execute(array('action'=>'display', 'tmpl' => 'posCreditPaymentForm', 'widget' => 'error', 'msg' => $checkReq->msg));
		}

		if(!isset($data['found']) || $data['found'] != 'yes'){
			$this->execute(array('action'=>'display', 'tmpl' => 'posCreditPaymentForm', 'widget' => 'error', 'msg' => 'Please Search for Transaction before submitting'));
		}



		#sanitize each of the fields & append to sanitized array
		$sanitized = array();
		foreach ($formFields as $key) {
			# code...
			if(in_array($key, array('amt1','amt2')) !== false){

				$newAmt = amtToInt($_POST[$key]);
				$$key = $registry->get('form')->sanitize($newAmt, 'float');

			}else{
				$$key = $registry->get('form')->sanitize($_POST[$key], 'string');
			}
			$sanitized[$key] = $$key ;

		}

		//var_dump($sanitized); die;

		# if deposit and confirm deposit values r not the same...throw error
		if($sanitized['amt2'] != $sanitized['amt1']){
			$this->execute(array('action'=>'display', 'tmpl' => 'posCreditPaymentForm', 'widget' => 'error', 'msg' => 'Amount & Confirm Amount must be the Same'));
		}

		# check if transaction with this transId exist
		$transaction = new Transaction(trim($sanitized['transId']));

		if(is_null($transaction->id)){
			$this->execute(array('action'=>'display', 'tmpl' => 'posCreditPaymentForm', 'widget' => 'error', 'msg' => 'No Transaction with TransId - ' . $sanitized['transId'] . ' was found'));
		}




	    switch(strtolower($sanitized['payType'])){
	    	case 'cash' :
	    		$det = array('Pay Type' => 'Cash');
	    	break;

	    	case 'pos':
	    		# code...
	    		$det = array('Pay Type' => 'POS', 'POS No' => $sanitized['posNo']);
	    		break;

	    	case 'postbill':
	    		# code...
	    	    $det = array('Pay Type' => 'Post Bill');
	    		break;
	    }


	    $registry->get('db')->addCreditPayment(array(
	    	'date' => $sanitized['date'],
	    	'transId' => $sanitized['transId'],
	    	'amt' => $sanitized['amt1'],
	    	'creditTransId' => $transaction->id,
	    	'details' => json_encode($det)
	    	));


	    # if it is a guest that made the payment
	    if((strtolower($sanitized['payType']) == 'cash' || strtolower($sanitized['payType']) == 'pos' ) && $sanitized['debtorType'] == 3){

	    	$checkInInfo = Guest::getCheckInInfo($sanitized['roomId']);
	    	$guest = new Guest($checkInInfo->id);

	    	# add guest Payment
	    	Guest::addPayment(array(
	    			'date' => $sanitized['date'],
	    			'guestId' => $guest->id,
	    			'transId' => $sanitized['transId'],
	    			'amt' => $sanitized['amt1'],
	    			'details' => json_encode($det)
	    			));
	    }



	    #if debtor type is staff
	    if($sanitized['debtorType'] == 1){

	    	#fetch from staff credit tbl Using transId to get the staff Id
	    	$check2 = $registry->get('db')->fetchStaffCreditByTransId(trim($sanitized['transId']));


	    	#add Staff payment
	    	$registry->get('db')->addStaffCreditPayment(array(
	    		'date' => today(),
	    		'transId' => $sanitized['transId'],
	    		'staffId' => $check2->staffId,
	    		'amt' => $sanitized['amt1']
	    		));
	    }

		# enquire if invioce is usually issued here...if so...print invioce

		#
	    $this->execute(array('action'=>'display', 'tmpl' => 'posCreditPaymentForm', 'widget' => 'success', 'msg' => 'Credit payment was successfully Added'));
	}

	public function setStaffCreditSearchParams(Array $data)
	{
		# code...
		  global $registry;
		  $session = $registry->get('session');

		  $staff = filter_var($data['staff'], FILTER_SANITIZE_NUMBER_INT);
		  $month = filter_var($data['month'], FILTER_SANITIZE_STRING);
		  $year = filter_var($data['year'], FILTER_SANITIZE_NUMBER_INT);

		  $session->write('staffDebtMonth', $month);
		  $session->write('staffDebtYear', $year);
		  $session->write('staffDebtStaff', $staff);

		  $this->execute(array('action'=>'render', 'tmpl' => 'staffCredits', 'widget' => '', 'msg' => ''));
	}

	public function fetchByTransId($transId)
	{
		# code...
		global $registry;
		$tId = filter_var($transId, FILTER_SANITIZE_STRING);

		$creditInfo = $registry->get('db')->fetchCreditByTransId($tId);
		$this->execute(array('action'=>'display', 'tmpl' => '', 'widget' => 'creditPaymentTypeOptions', 'msg' => $creditInfo));
	}

	/**
	 * @param array $data
	 * @throws Exception
     */
	public function addSubcharge(Array $data){
		global $registry;

		# check required fields
		$required = array('date', 'staffId', 'amt1', 'amt2', 'reason');

		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}

		$checkReq = json_decode($registry->get('form')->checkRequiredFields($required));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			$this->execute(array('action'=>'display', 'tmpl' => 'addSubcharge', 'widget' => 'error', 'msg' =>
					$checkReq->msg));
		}

		#sanitize each of the fields & append to sanitized array
		$sanitized = array();
		foreach ($formFields as $key) {
			# code...
			if(in_array($key, array('amt1','amt2')) !== false){

				$newAmt = amtToInt($_POST[$key]);
				$$key = $registry->get('form')->sanitize($newAmt, 'float');

			}else{
				$$key = $registry->get('form')->sanitize($_POST[$key], 'string');
			}
			$sanitized[$key] = $$key ;

		}

		$registry->get('db')->addStaffSubcharge(array(
				'date' => $sanitized['date'],
				'staffId' => $sanitized['staffId'],
				'amt' => $sanitized['amt1'],
				'reason' => $sanitized['reason']
		));
		$staff = new Staff($sanitized['staffId']);
		$msg = 'Subcharge of ' . number_format($sanitized['amt1']) . ' was successfully added for ' . $staff->name;

		$this->execute(array('action'=>'display', 'tmpl' => 'addSubcharge', 'widget' => 'success', 'msg' => $msg));



	}


	public function setStaffSubchargeSearchParams(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$staff = filter_var($data['staff'], FILTER_SANITIZE_NUMBER_INT);
		$month = filter_var($data['month'], FILTER_SANITIZE_STRING);
		$year = filter_var($data['year'], FILTER_SANITIZE_NUMBER_INT);

		$session->write('staffSubchargeMonth', $month);
		$session->write('staffSubchargeYear', $year);
		$session->write('staffSubchargeStaff', $staff);

		$this->execute(array('action'=>'render', 'tmpl' => 'viewSubcharges', 'widget' => '', 'msg' => ''));
	}


	public function setStaffShortagesSearchParams(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$staff = filter_var($data['staff'], FILTER_SANITIZE_NUMBER_INT);
		$month = filter_var($data['month'], FILTER_SANITIZE_STRING);
		$year = filter_var($data['year'], FILTER_SANITIZE_NUMBER_INT);

		$session->write('staffShortageMonth', $month);
		$session->write('staffShortageYear', $year);
		$session->write('staffShortageStaff', $staff);

		$this->execute(array('action'=>'render', 'tmpl' => 'viewShortages', 'widget' => '', 'msg' => ''));
	}






	#end of class
}
