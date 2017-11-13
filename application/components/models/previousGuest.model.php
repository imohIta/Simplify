<?php
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class PreviousGuestModel extends BaseModel{
	
	protected $_param;
	protected $_viewParams;

	/*
	* this function displays widgets ( err alerts, success alerts etc ) and renders pages ( check in, checkout etc )
	* param action : display ( in the case of a widget i.e the view will not load a new Page but will load the widgget on the tmpl value page passed )
	*				 render ( in the case of a tmpl, here the view will load the tmpl page without any widget) 
	* param msg : the message to be display on a widget...always empty if no widget is to be loaded
	*
	*/
	public function execute(Array $options = array('action'=>'render', 'tmpl' => 'previousGuestOptions', 'widget' => '', 'msg' => '')){
		$this->_viewParams = $options;
		$this->notify();
	}

	public function showStayHistory()
	{
		# code...
		$this->execute(array('action'=>'render', 'tmpl' => 'stayHistory', 'widget' => '', 'msg' => ''));
	}

	public function fetchStayRecord($guestPhone)
	{
		# code...
		global $registry;
		$data = Guest::fetchPreviousGuestDetails($guestPhone);
		
		for ($i=0; $i <= count($data)-1; $i++) { 
			$bills = json_decode($data[$i]['bills']);
			$newB = array();
			for ($b=0; $b <= count($bills)-1; $b++) { 
				$bill = $bills[$b];
				if($b < count($bills)){
				$room = new Room($bill->roomId);
				$bill->roomNo = $room->no;
				array_push($newB, $bill);
			   }
			}
			$data[$i]['bills'] = json_encode($newB);
			
		}
		
		$this->execute(array('action'=>'display', 'tmpl' => '', 'widget' => 'showStayRecords', 'msg' => $data));
		
	}


	public function fetchCreditsAndPayments($guestPhone)
	{
		# code...
		global $registry;
		$credits = Guest::fetchPreviousGuestCredits($guestPhone);
		$payments = Guest::fetchPreviousGuestPayments($guestPhone);
		$this->execute(array('action'=>'display', 'tmpl' => '', 'widget' => 'showPreviousGuestCredits', 'msg' => array('credits' => $credits, 'payments' => $payments)));
		
	}

	public function showCreditPaymentForm()
	{
		# code...
		$this->execute(array('action'=>'render', 'tmpl' => 'pgCreditPayment', 'widget' => '', 'msg' => ''));
	}

	public function submitCreditPayment($data)
	{
		# code...
		global $registry; 

		$requiredFields = array('payType', 'amt1', 'amt2', 'phone');
		# get all form fields into an array...
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}

		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){

			#set sessions for filled so that the user does not have the refill the form over again
			foreach ($formFields as $value) {
				
				# code...
				if(isset($_POST[$value]) && (!empty($_POST[$value]) && is_int($_POST[$value])) ){
					$session->write($value, $_POST[$value]);
				}
			}
			
			$this->execute(array('action'=>'display', 'tmpl' => 'pgCreditPayment', 'widget' => 'error', 'msg' => $checkReq->msg));
		}

		#sanitize each of the fields & append to sanitized array
		$sanitized = array();
		foreach ($formFields as $key) {
			# code...
			if(in_array($key, array('amt1','amt2','guestId')) !== false){
				if(in_array($key, array('amt1','amt2')) !== false){
					$newAmt = amtToInt($_POST[$key]);
					$$key = $registry->get('form')->sanitize($newAmt, 'float');
			    }else{
			    	$$key = $registry->get('form')->sanitize($_POST[$key], 'int');
			    }
			}else{
				$$key = $registry->get('form')->sanitize($_POST[$key], 'string');
			}
			$sanitized[$key] = $$key ;

		}


		if(!isset($sanitized['guestId'])){
			$this->execute(array('action'=>'display', 'tmpl' => 'pgCreditPayment', 'widget' => 'error', 'msg' => 'Please Click Search after entering the guest Phone No. before submitting'));
		}

		if($sanitized['amt1'] != $sanitized['amt2']){
			$this->execute(array('action'=>'display', 'tmpl' => 'pgCreditPayment', 'widget' => 'error', 'msg' => 'Amount and Confirm Amount Values must be the Same'));
		}

		if(isset($sanitized['payType'])){

		    switch(strtolower($sanitized['payType'])){
		    	case 'cash' : case 'default' : case '' :
		    		$det = array('Pay Type' => 'Cash');
		    	break;

		    	case 'cheque':
		    		# code...
		    	    $det = array('Pay Type' => 'Cheque', 'Bank' => $sanitized['chequeBank'], 'Cheque No' => $sanitized['chequeNo']);
		    		break;

		    	case 'pos':
		    		# code...
		    		$det = array('Pay Type' => 'POS', 'POS No' => $sanitized['posNo']);
		    		break;

		    	case 'bt':
		    		# code...
		    	    $det = array('Pay Type' => 'Bank Transfer', 'Bank' => $sanitized['btBank'], 'Transfer Date' => $sanitized['btDate']);
		    		break;
		    }
		}else{
			$det = array('Pay Type' => 'Cash');
		}

		$guest = new Guest($sanitized['guestId']);

		if(Guest::addCreditPayment(array(
							'date' => today(),
							'guestId' => $sanitized['guestId'],
							'guestPhone' => $sanitized['phone'],
							'transId' => generateTransId(),
							'amt' => $sanitized['amt1'],
							'details' => json_encode($det)
							)) ){

			
			$this->execute(array('action'=>'display', 'tmpl' => 'pgCreditPayment', 'widget' => 'success', 'msg' => 'Payment successfully added for ' . $guest->name));
		}else{
			$this->execute(array('action'=>'display', 'tmpl' => 'pgCreditPayment', 'widget' => 'error', 'msg' => 'Payment could not be added for ' . $guest->name . '. Please try again'));
		}


	}

	public function fetchCreditBal($guestPhone)
	{
		# code...
		global $registry;
		$credits = Guest::fetchPreviousGuestCredits($guestPhone);
		$payments = Guest::fetchPreviousGuestPayments($guestPhone);

		$tc = 0;
		foreach ($credits as $key) {
			# code...
			$tc += $key->amt;
		}

		$tp = 0;
		foreach ($payments as $key) {
			# code...
			$tp += $key->amt;
		}

		echo  $tc - $tp; 
		
	}



	#end of class
}

