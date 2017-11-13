<?php
/**
*
*
*/
defined('ACCESS') || Error::exitApp(); 

class GuestModel extends BaseModel{
	
	protected $_param;
	protected $_viewParams;
 
	/* 
	* this function displays widgets ( err alerts, success alerts etc ) and renders pages ( check in, checkout etc )
	* param action : display ( in the case of a widget i.e the view will not load a new Page but will load the widgget on the tmpl value page passed )
	*				 render ( in the case of a tmpl, here the view will load the tmpl page without any widget) 
	* param msg : the message to be display on a widget...always empty if no widget is to be loaded
	*
	*/
	public function execute(Array $options = array('action' => 'render', 'tmpl' => 'viewAllGuest', 'widget' => '', 'msg' => '')){
		$this->_viewParams = $options;
		$this->notify();
	}


	public function showCheckInOptions($value='')
	{
		# code...
		$this->execute(array('action'=>'render', 'tmpl' => 'CheckInOptions', 'widget' => '', 'msg' => ''));
	}

	public function showCheckInForm()
	{
		# code...
		$this->execute(array('action'=>'render', 'tmpl' => 'CheckIn', 'widget' => '', 'msg' => ''));
	}

	public function showComCheckInForm()
	{
		# code...
		$this->execute(array('action'=>'render', 'tmpl' => 'ComplimentaryCheckIn', 'widget' => '', 'msg' => ''));
	}
	

	public function showCheckOutForm()
	{
		# code...
		$this->execute(array('action'=>'render', 'tmpl' => 'CheckOut', 'widget' => '', 'msg' => ''));
	}

	public function submitCheckIn(Array $values)
	{
		global $registry;
		$session = $registry->get('session');

		//var_dump($values); die;
        
		#required fields
		$required = array('phone', 'name', 'addr', 'nationality', 'reason', 'roomType', 'roomNo', 'payType');

		$sanitized = $this->_processCheckInForm($values, $required);

		# if deposit and confirm deposit values r not the same...throw error
		if($sanitized['deposit2'] != $sanitized['deposit1']){
			$this->execute(array('action'=>'display', 'tmpl' => 'CheckIn', 'widget' => 'error', 'msg' => 'Deposit & Confirm Deposit Amounts must be the Same'));
		}

		Guest::checkIn($sanitized);
				
		#print Invioce
	    $session->write('showInvioce', true);
	    $session->write('invioceType', 'Routine');
	    $session->write('invioceData', $sanitized);
	    $registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/guest/printCheckInInvioce');
			    

		/*}else{
			
			foreach ($sanitized as $value) {
				
				# code...
				if(isset($_POST[$value]) && !empty($_POST[$value])){
					$session->write($value, $_POST[$value]);
				}
			}

			$msg = "Guest Could not be checked In...Please try again later";
			$this->execute(array('action'=>'display', 'tmpl' => 'CheckIn', 'widget' => 'error', 'msg' => $msg));

		}*/



	}

	public function submitComCheckIn(Array $values)
	{
		# code...
		global $registry;
		$session = $registry->get('session');
        
		#required fields
		$required = array('phone', 'name', 'addr', 'nationality', 'reason', 'roomType', 'roomNo');

		$sanitized = $this->_processCheckInForm($values, $required);

		# todo : check if guest is already checked in

		
		# check In guest as Complimentary
		Guest::checkIn($sanitized, true);
		
		
		#print Invioce
	    $session->write('showInvioce', true);
	    $session->write('invioceType', 'Complimentary');
	    $session->write('invioceData', $sanitized);
	    $registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/guest/printCheckInInvioce');


		/*}else{
			
			foreach ($formFields as $value) {
				
				# code...
				if(isset($_POST[$value]) && !empty($_POST[$value])){
					$session->write($value, $_POST[$value]);
				}
			}

			$msg = "Guest Could not be checked In..Please try again later";
			$this->execute(array('action'=>'display', 'tmpl' => 'CheckIn', 'widget' => 'error', 'msg' => $msg));

		}*/
	}

	public function submitFlatRateCheckIn(Array $values)
	{
		global $registry;
		$session = $registry->get('session');

		//var_dump($values); die;

		#required fields
		$required = array('phone', 'name', 'addr', 'nationality', 'reason', 'bill', 'roomType', 'roomNo',
		'payType');

		$sanitized = $this->_processCheckInForm($values, $required);


		# if deposit and confirm deposit values r not the same...throw error
		if($sanitized['deposit2'] != $sanitized['deposit1']){
			$this->execute(array('action'=>'display', 'tmpl' => 'CheckIn', 'widget' => 'error', 'msg' => 'Deposit & Confirm Deposit Amounts must be the Same'));
		}

		Guest::checkIn($sanitized, false, true);

		#print Invioce
		$session->write('showInvioce', true);
		$session->write('invioceType', 'Routine');
		$session->write('invioceData', $sanitized);
		$registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/guest/printCheckInInvioce');


		/*}else{

			foreach ($sanitized as $value) {

				# code...
				if(isset($_POST[$value]) && !empty($_POST[$value])){
					$session->write($value, $_POST[$value]);
				}
			}

			$msg = "Guest Could not be checked In...Please try again later";
			$this->execute(array('action'=>'display', 'tmpl' => 'CheckIn', 'widget' => 'error', 'msg' => $msg));

		}*/



	}


	private function _processCheckInForm(Array $values, Array $requiredFields)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		# get all form fields into an array...
		$formFields = array();
		foreach ($values as $key => $value) {
			# code...
			$formFields[] = $key;
		}
		
		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){

			#set sessions for filled so that the user does not have the refill the form over again
			foreach ($formFields as $value) {
				
				# code...
				//if(isset($_POST[$value]) && (!empty($_POST[$value]) && is_int($_POST[$value])) ){
					$session->write($value, $_POST[$value]);
				//}
			}
			
			$this->execute(array('action'=>'display', 'tmpl' => 'CheckIn', 'widget' => 'error', 'msg' => $checkReq->msg));
		}

		

		#sanitize each of the fields & append to sanitized array
		$sanitized = array();
		foreach ($formFields as $key) {
			# code...
			if(in_array($key, array('deposit1','deposit2', 'noOfOccupants', 'discount', 'outBal', 'bill')) !==
			   false){
				if(in_array($key, array('deposit1','deposit2', 'bill')) !== false){
					if(!isset($_POST[$key]) || !$_POST[$key]){
						$newAmt = 0;
					}else{
						$newAmt = amtToInt($_POST[$key]);
				    }
					$$key = $registry->get('form')->sanitize($newAmt, 'float');
				}else{
					$$key = $registry->get('form')->sanitize($_POST[$key], 'int');
				}
			}else{
				$$key = $registry->get('form')->sanitize($_POST[$key], 'string');
			}
			$sanitized[$key] = $$key ;

		}

		return $sanitized;
	}


	public function printCheckInInvioce()
	{
		# code...
		$this->execute(array('action'=>'render', 'tmpl' => 'checkInInvioce', 'widget' => '', 'msg' => ''));
	}

	public function printGuestPaymentInvioce()
	{
		# code...
		$this->execute(array('action'=>'render', 'tmpl' => 'guestPaymentInvioce', 'widget' => '', 'msg' => ''));
	}

	public function fetchTransactions($guestId)
	{
		# roomId here might not be important...just passing it incase of future use

		# code...
		global $registry;

		# create new guest object
		$guest = new Guest($guestId);

		$bills = $guest->fetchBills();
		//var_dump($bills); die;
		$newBills = array();
		
		foreach ($bills as $key) {
			# code...
			$room = new Room($key['roomId']);
			$key['roomNo'] = $room->no;
			$newBills[] = $key;
		}
		$bills = $newBills;

		$payments = $guest->fetchPayments();

		$totalBill = is_null($guest->fetchTotalBills()) ? 0 : $guest->fetchTotalBills() ;

		$totalPayment = is_null($guest->fetchTotalPayments()) ? 0 : $guest->fetchTotalPayments();

		echo json_encode(array('bills' => $bills, 'totalBill' => $totalBill, 'payments' => $payments, 'totalPayment' => $totalPayment));

	}

	

	public function showCheckOutOptions(Array $data)
	{
		# code...
		if($data['totalBill'] > $data['totalPayment']){
			$this->execute(array('action'=>'display', 'tmpl' => '', 'widget' => 'checkOutOptions', 'msg' => array('type' => '1', 'diff' => $data['totalBill'] - $data['totalPayment'], 'guestId' => $data['guestId'], 'roomId'=> $data['roomId'] )));
		}else{
			$this->execute(array('action'=>'display', 'tmpl' => '', 'widget' => 'checkOutOptions', 'msg' => array('type' => '2', 'diff' => $data['totalPayment'] - $data['totalBill'], 'guestId' => $data['guestId'], 'roomId'=> $data['roomId'] )));
		}
	}

	public function submitCheckOut($data)
	{
		global $registry;

		$db = $registry->get('db');

		$guest = new Guest($data['guestId']);
		$chInfo = $guest->getCheckInInfo2();

		$room = new Room($data['roomId']);

		$this->_registerCheckOutEvents();
		
		# if guest bill & payment are not the same
		if(isset($data['type'])){
			
			# if guest bill is more than the payment
			if($data['type'] == 1){
                 
                 # if user collected balance payment from guest
            	if($data['switch-radio'] == '1'){

            		#if user chooses make payment option...
					if(isset($data['payType'])){

					    switch(strtolower($data['payType'])){
					    	case 'cash': case 'default' :
					    		$det = array('Pay Type' => 'Cash');
					    	break;

					    	case 'cheque':
					    		# code...
					    	    $det = array('Pay Type' => 'Cheque', 'Bank' => $data['chequeBank'], 'Cheque No' => $data['chequeNo']);
					    		break;

					    	case 'pos':
					    		# code...
					    		$det = array('Pay Type' => 'POS', 'POS No' => $data['posNo']);
					    		break;

					    	case 'bt':
					    		# code...
					    	    $det = array('Pay Type' => 'Bank Transfer', 'Bank' => $data['btBank'], 'Transfer Date' => $data['btDate']);
					    		break;

					    }
					   
				    }else{ # if user did not choose a pay type...default to cash
				   		$det = json_encode(array('Pay Type' => 'Cash'));
				    } 

			   		Guest::addPayment(array(
									   		'date' => today(),
									   		'guestId' => $data['guestId'],
									   		'transId' => generateTransId(),
									   		'amt' => $data['diff'],
									   		'details' => json_encode($det)
									   		));
            		

            	}else{


            		#if user made credit checkOut
            		$this->addEventListener('creditCheckOut', $registry->get('guestDb'), 'addCredit');
			    	$this->addEventListener('creditCheckOut', $registry->get('logger'), 'logGuestCreditCheckOut');

			    	$this->triggerEvent('creditCheckOut', array($data['guestId'], $data['diff'], $room->id));
            	}

            }else{ # if the guest payment is more than his/her bills

            	
            	# if user made cash refund to guest
            	if($data['switch-radio'] == '1'){

            		# Add guest refund
            		Guest::addRefund(array(
            							 'date' => today(),
            							 'guestId' => $data['guestId'],
            							 'transId' => generateTransId(),
            							 'amt' => $data['diff']
            							));	

            	}else{ #if user sent guest Bal to outstanding balances

            		# add guest outstanding balance
            		Guest::addOutstandingBal(array(
            									'phone' => $guest->phone,
            									'amt' => $data['diff']
            									));

            	}


           }
     
        }

       # trigger event for guest check out
       $this->triggerEvent('checkOutGuest', array($data['guestId'], $room->id));
       
       $registry->get('session')->write('formMsg', 'Guest ( ' . $guest->name . ' ) occupying room ' . $room->no . ' was successfully checked out');

      #redirect
       $uri = $registry->get('config')->get('baseUri') . '/guest/checkOut';
       $registry->get('uri')->redirect($uri);

	}

	protected function _registerCheckOutEvents(){
		global $registry;
		$db = $registry->get('db');

		#register event listerners
		$this->addEventListener('checkOutGuest', $this, '_compileGuestStayActivity');
		$this->addEventListener('checkOutGuest', $this, '_deleteBillsAndPayments');
		$this->addEventListener('checkOutGuest', $this, '_manipulateBillPayer');
		$this->addEventListener('checkOutGuest', $this, '_deleteGuestFromRegister');

		# Sms Notifier
		//$this->addEventListener('checkOutGuest', $registry->get('notifier'), 'sendLogoutGreeting');
	}

	protected function _compileGuestStayActivity(Array $data)
	{
		# code...
		global $registry;

		$guest = new Guest($data[0]);
		$bills = $guest->fetchBills();
		$payments = $guest->fetchPayments();

		# get guest checkIn date & roomId from guest Register
		$cInfo = $guest->getCheckInInfo2();

		$room = new Room($data[1]);

		$data = array(
						'guestPhone' => $guest->phone,
						'stayInfo' => json_encode(array('checkInDate' => $cInfo->checkInDate, 'checkOutDate' => today(), 'roomNo' => $room->no, 'roomType' => $room->type)),
						'bills' =>json_encode($bills), 
						'payments' => json_encode($payments)
						);
		Guest::addStayActivity($data);
	}

	protected function _deleteBillsAndPayments(Array $data)
	{
		# code...
		global $registry;

		$guest = new Guest($data[0]);

		$guest->deleteBills();
		$guest->deletePayments();
	}

	protected function _deleteGuestFromRegister(Array $data)
	{
		# code...
		Guest::deleteFromRegister($data[0], $data[1]);
	}

	protected function _manipulateBillPayer(Array $data)
	{
		global $registry;
		$guest = new Guest($data[0]);

		$room = new Room($data[1]);

		# check if this guest is a bill payer for any other room outside his own
		$check = Bill::selectBillPayerOtherRooms($guest->id, $room->id);
		
		# if so edit bill payers...make the person staying in the room to become the bill payer for dat room
		if(!empty($check) && $check !== null && $check !== false){
			
			//var_dump($check); die;
			#for each of the rooms
			foreach ($check as $key) {
				# get the guest Info occupying the rooms
				$d = Guest::getCheckInInfo($key->roomId);

				#update bill payers
				Bill::updateBillPayer($d->id, $key->roomId, json_decode($key->billTypes, true));

				# delete guest as bill payer for this room
				Guest::deleteFromBillPayers($guest->id, $key->roomId);
			}
		}

		# delete this guest as bill payer for his/her own room
		Guest::deleteFromBillPayers($guest->id, $room->id);
	}

	public function showTransactionOptions()
	{
		$this->execute(array('action'=>'render', 'tmpl' => 'transactionOptions', 'widget' => '', 'msg' => ''));
	}

	public function showTransaction()
	{
		$this->execute(array('action'=>'render', 'tmpl' => 'showTransactions', 'widget' => '', 'msg' => ''));
	}

	public function showAddBill()
	{
		$this->execute(array('action'=>'render', 'tmpl' => 'addBill', 'widget' => '', 'msg' => ''));
	}

	public function showAddPayment()
	{
		$this->execute(array('action'=>'render', 'tmpl' => 'addPayment', 'widget' => '', 'msg' => ''));
	}

	public function submitAddGuestBill(Array $data)
	{
		global $registry; 


		$requiredFields = array('desc', 'amt1', 'amt2');
		
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
			
			$this->execute(array('action'=>'display', 'tmpl' => 'addBill', 'widget' => 'error', 'msg' => $checkReq->msg));
		}

		#sanitize each of the fields & append to sanitized array
		$sanitized = array();
		foreach ($formFields as $key) {
			# code...
			if(in_array($key, array('amt1','amt2','guestId', 'roomId')) !== false){
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

		if($sanitized['amt1'] != $sanitized['amt2']){
			$this->execute(array('action'=>'display', 'tmpl' => 'addBill', 'widget' => 'error', 'msg' => 'Amount and Confirm Amount Values must be the Same'));
		}

		# select all bill payers for the room
		foreach (Bill::selectBillPayersForRoom($sanitized['roomId']) as $row) {
			# code...
			$billsCovered = json_decode($row->billTypes, true);

			# if the bill type also contains Reception or All
			if(in_array('Reception', $billsCovered) !== false || in_array('All', $billsCovered) !== false){

				# Assign guest Id as Bill Payer
				$g = $row->guestId;
				break;
			}

		}

		$guest = new Guest($sanitized['guestId']);
		$payer = new Guest($g);

		#Add Bill
		if( Guest::addBill(array(
							'date' => today(),
							'guestId' => $payer->id,
							'roomId' => $sanitized['roomId'],
							'transId' => generateTransId(),
							'amt' => $sanitized['amt1'],
							'billType' => 6,
							'details' => $sanitized['desc']
							)) ){
			$this->execute(array('action'=>'display', 'tmpl' => 'addBill', 'widget' => 'success', 'msg' => 'Bill successfully added for ' . $guest->name));
		}else{
			$this->execute(array('action'=>'display', 'tmpl' => 'addBill', 'widget' => 'error', 'msg' => 'Bill could not be added for ' . $guest->name . '. Please try Again'));
		}

	}


	public function submitAddGuestPayment(Array $data)
	{
		global $registry; 
		$session = $registry->get('session');

		$requiredFields = array('payType', 'amt1', 'amt2');
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
			
			$this->execute(array('action'=>'display', 'tmpl' => 'addPayment', 'widget' => 'error', 'msg' => $checkReq->msg));
		}

		#sanitize each of the fields & append to sanitized array
		$sanitized = array();
		foreach ($formFields as $key) {
			# code...
			if(in_array($key, array('amt1','amt2','guestId', 'roomId')) == true){
				$newAmt = amtToInt($_POST[$key]);
				$$key = $registry->get('form')->sanitize($newAmt, 'float');
			}else{
				$$key = $registry->get('form')->sanitize($_POST[$key], 'string');
			}
			$sanitized[$key] = $$key ;

		}

		if($sanitized['amt1'] != $sanitized['amt2']){
			$this->execute(array('action'=>'display', 'tmpl' => 'addPayment', 'widget' => 'error', 'msg' => 'Amount and Confirm Amount Values must be the Same'));
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

		$transId = generateTransId();

		#Add Bill
		if( Guest::addPayment(array(
							'date' => today(),
							'guestId' => $guest->id,
							'transId' => $transId,
							'amt' => $sanitized['amt1'],
							'details' => json_encode($det)
							)) ){
			
			# Print Invioce
			$invioceData = array(
				'guestName' => $guest->name,
				'guestPhone' => $guest->phone,
				'transId' => $transId,
				'uri' => $registry->get('config')->get('baseUri') .'/guest/addPayment',
				'amt' => $sanitized['amt1']
			);

			$session->write('showInvioce', true);
		    $session->write('invioceType', 'Payment');
		    $session->write('invioceData', $invioceData);
		    $registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/guest/printGuestPaymentInvioce');

		}else{
			$this->execute(array('action'=>'display', 'tmpl' => 'addPayment', 'widget' => 'error', 'msg' => 'Payment could not be added for ' . $guest->name . '. Please try Again'));
		}
	}

	public function showChangeRoomForm()
	{
		# code...
		$this->execute(array('action'=>'render', 'tmpl' => 'changeRoom', 'widget' => '', 'msg' => ''));
	}

	public function submitChangeRoom($data)
	{
		# code...
		global $registry;

		$requiredFields = array('guestId', 'oldRoomId', 'roomNo', 'reason');
		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			
			$this->execute(array('action'=>'display', 'tmpl' => 'changeRoom', 'widget' => 'error', 'msg' => $checkReq->msg));
		}
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}
		
		$sanitized = array();
		foreach ($formFields as $key) {
			
			# code...
			if(in_array($key, array('guestId', 'roomNo', 'oldRoomId')) == true){
				$$key = $registry->get('form')->sanitize($_POST[$key], 'float');
			}else{
				$$key = $registry->get('form')->sanitize($_POST[$key], 'string');
			}
			$sanitized[$key] = $$key ;

		}
		
		if(!empty($data) && !is_null($data)){

			# change bill payer room
			Bill::changeBillPayerRoom(array(
				  			'guestId' => $sanitized['guestId'], 
				  			'oldRoomId' => $sanitized['oldRoomId'], 
				  			'newRoomId' => $sanitized['roomNo']
				  			));

			# update guest Bill been if Guest has charged Room charge for today 
			$oldRoom = new Room($sanitized['oldRoomId']);
			$newRoom = new Room($sanitized['roomNo']);

			if($oldRoom->price != $newRoom->price){
				
				# calculate Room Price Considering the discount the guest has
				$dis = ( $sanitized['discount'] / 100 ) * $newRoom->price;
				$newAmt = $newRoom->price - $dis;

				$transId = Guest::updateBills(array(
									'date' => today(),
									'guestId' => $sanitized['guestId'],
									'amt' => $newAmt,
									'billType' => 2
									));
				
				
				//echo $transId; die;

				#update Transactions
				if(!is_null($transId) && $transId !== false){

					# select bill ( room charge ) from transactions table using the transId
 					$transaction = new Transaction($transId);

 					

 					# extract details in order to get the amt
					$tDetails = json_decode($transaction->details);
					
					# Set amt in transacto details to the new Rooms Price
					$tDetails->amt = $newAmt;

					//var_dump($tDetails); die;

					$transaction->update(array(
								'details' => json_encode($tDetails)
								));
				}

			}


			# change guest Room in guest register
			Guest::updateRoomInRegister(array(
				  			'guestId' => $sanitized['guestId'], 
				  			'oldRoomId' => $sanitized['oldRoomId'], 
				  			'newRoomId' => $sanitized['roomNo']
				  			));

			# change guest Room in Bills
			Bill::changeGuestBillsRoomId(array(
						'guestId' => $sanitized['guestId'],
						'oldRoomId' => $sanitized['oldRoomId'], 
				  		'newRoomId' => $sanitized['roomNo']
					));

			
			$registry->get('logger')->logRoomChange(array(
							'guestId' => $sanitized['guestId'],
							'oldRoomId' => $sanitized['oldRoomId'],
							'newRoomId' => $sanitized['roomNo']
							));

			$this->execute(array('action'=>'display', 'tmpl' => 'changeRoom', 'widget' => 'success', 'msg' => 'Guest Room has been successfully changed from Room ' . $oldRoom->no . ' to Room ' . $newRoom->no));
		}else{
			$this->execute(array('action'=>'display', 'tmpl' => 'changeRoom', 'widget' => 'error', 'msg' => 'Room Change Unsucessfull...Please try Again'));
		}



	}

	public function showManageGuestOptions()
	{
		# code...
		$this->execute(array('action'=>'render', 'tmpl' => 'manageGuestOptions', 'widget' => '', 'msg' => ''));
	}

	public function showManageDiscount()
	{
		$this->execute(array('action'=>'render', 'tmpl' => 'manageGuestDiscount', 'widget' => '', 'msg' => ''));
	}

	public function submitDiscountChange($data)
	{
		# code...
		global $registry;

		$requiredFields = array('guestId', 'oldDiscount', 'newDiscount', 'roomId');
		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			
			$this->execute(array('action'=>'display', 'tmpl' => 'manageGuestDiscount', 'widget' => 'error', 'msg' => $checkReq->msg));
			return;
			
		}
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}
		
		$sanitized = array();
		foreach ($formFields as $key) {
			
			# code...
			if(in_array($key, array('guestId', 'roomNo', 'oldRoomId')) == true){
				$$key = $registry->get('form')->sanitize($_POST[$key], 'float');
			}else{
				$$key = $registry->get('form')->sanitize($_POST[$key], 'string');
			}
			$sanitized[$key] = $$key ;

		}

		if($sanitized['oldDiscount'] != $sanitized['newDiscount']){

				if(Guest::updateDiscount(array(
									'guestId' => $sanitized['guestId'],
									'roomId' => $sanitized['roomId'],
									'discount' => $sanitized['newDiscount']
									)) ){
					
					# Log Guest Discount Change
					$registry->get('logger')->logDiscountChange(array(
											'guestId' => $sanitized['guestId'],
											'roomId' => $sanitized['roomId'],
											'oldDiscount' => $sanitized['oldDiscount'],
											'newDiscount' => $sanitized['newDiscount']
											));
									}
			}

			$this->execute(array('action'=>'display', 'tmpl' => 'manageGuestDiscount', 'widget' => 'success', 'msg' => 'Guest Discount successfully Changed from ' . $sanitized['oldDiscount'] . ' % to ' . $sanitized['newDiscount'] . ' %'));
		return;

		// }else{
		// 	$this->execute(array('action'=>'display', 'tmpl' => 'manageGuestDiscount', 'widget' => 'error', 'msg' => 'Guest Discount could not be changed...Please try Again'));
		// }
	}

	
	public function submitTransferExpenses($data)
	{
		global $registry;

		
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}
		
		$sanitized = array();
		foreach ($formFields as $key) {
			
			# code...
			if(in_array($key, array('billType', 'startTransferFrom')) !== false){
				$$key = $registry->get('form')->sanitize($_POST[$key], 'string');
			}else{
				$$key = $registry->get('form')->sanitize($_POST[$key], 'float');
			}
			$sanitized[$key] = $$key ;

		}

		$payer = new Guest($sanitized['payer']);
		$beneficiary = new Guest($sanitized['beneficiary']);


		$payerRoom = new Room($sanitized['payerRoom']);
		$beneficiaryRoom = new Room($sanitized['beneficiaryRoom']);

		switch ($sanitized['startTransferFrom']) {
			case '0': case 'today': default:
				# code...
			    $sDate = today();
				break;

			case 'checkInDate':   
				$ck = Guest::getCheckInInfo($beneficiaryRoom->id);
				$sDate = $ck->checkInDate;
				break;
			case 'chooseDate':
				$sDate = $sanitized['transferStartDate'];
				if($sDate == ''){ $sDate = today(); }
				break;
		}
		# paying Guest ( pg) -  guest2 - room 2
		# benefactor guest ( bg )  - guest 1 - guest 1

		
		# if user did not choose any bill type...or unset bill type that was der previously
		if(!isset($sanitized['billType'])){
			

			# select all bills that the payer was pying for the beneficiary
			$payerBills = Bill::getBillsCoveredByGuestForRoom($payer->id, $beneficiaryRoom->id);
			
			if(!empty($payerBills) && !is_null($payerBills)){
				
				$beneficiaryBills = Bill::getBillsCoveredByGuestForRoom($beneficiary->id, $beneficiaryRoom->id);

				$pTypes = json_decode($payerBills['billTypes'], true);
				$bTypes = json_decode($beneficiaryBills['billTypes'], true);

				# if beneficiary is not paying any of his room bills
				if($bTypes[0] == 'None'){
					$bTypes = array();
				}
	    		
	    		# for each of billTypes ( payers bills for beneficairy's room which is now the new BillTypes that beneficiary shud cover for his room )
	    		foreach ($pTypes as $key) {
	    			# if key is not in billTypes currently covered by beneficairy for his room
	    			if(in_array($key, $bTypes) === false){
	    				$bTypes[] = $key;
	    			}
	    		}

	    		# if beneficairy bill type contain all the available bill types...change billtypes to all
	    		if(in_array('Room Charge', $bTypes) !== false && in_array('Main Bar', $bTypes) !== false && in_array('Pool Bar', $bTypes) !== false && in_array('Reception', $bTypes) !== false && in_array('Resturant', $bTypes) !== false ){
	    			$bTypes = array('All');
	    		}

				# update beneficiary's bill...all bill types selected above
				Bill::updateBillPayer2($beneficiary->id, $beneficiaryRoom->id, json_encode($bTypes));

			}

			# delete billPayer for payer for beneficiary's room
			Guest::deleteFromBillPayers($payer->id, $beneficiaryRoom->id);

			# update bills Table...make beneficairy to own his bills begining from startdate
			Guest::changeIdInBillsTbl(array(
									'roomId' => $beneficiaryRoom->id,
									'newGuestId' => $beneficiary->id,
									'startDate' => $sDate,
									'billTypes' => 'All'
									));

			$this->execute(array('action'=>'display', 'tmpl' => 'transferExpenses', 'widget' => 'success', 'msg' => 'Guest Expenses for Guest ( ' . $beneficiary->name . ' ) staying in Room ( ' . $beneficiaryRoom->no .' ) was successfully transfered to Guest ( ' . $payer->name . ' ) staying in Room ( ' . $payerRoom->no . ' )' ));
		
		}else{

				# if pg is paying all of bg's bills
				if($sanitized['billType'] == 'all'){
					
					# insert new billPayer for payer ( paying for beneficiary's room )
					Guest::addAsBillPayer(array(
									'guestId' => $payer->id,
									'roomId' => $beneficiaryRoom->id,
									'billTypes' => json_encode(array('All'))
									));

					# delete billPayer for bg
					//$registry->get('db')->deleteGuestFromBillPayers($beneficiary->id, $beneficiaryRoom->id);

					Bill::updateBillPayer2($beneficiary->id, $beneficiaryRoom->id, json_encode(array('None')));

					Guest::changeIdInBillsTbl(array(
									'roomId' => $beneficiaryRoom->id,
									'newGuestId' => $payer->id,
									'startDate' => $sDate,
									'billTypes' => 'All'
									));


				}else{
					# if Payer paying some of bg's bills


					if($sanitized['billType'] == 'roomCharge'){
						$pgBills = json_encode(array('Room Charge'));
						$bgBills = json_encode(array('Main Bar', 'Pool Bar', 'Reception', 'Resturant'));

					}else{

						$bgBills = json_encode(array('Room Charge'));
						$pgBills = json_encode(array('Main Bar', 'Pool Bar', 'Reception', 'Resturant'));

					}


					# insert new billPayer for payer ( paying for beneficiary's room )
					
					# check if payer is already covering any beneficiary room bill
					$bills = Bill::getBillsCoveredByGuestForRoom($payer->id, $beneficiaryRoom->id);
					//var_dump($bills); die;
					
					# if not true ... add
					if(empty($bills) || is_null($bills) || $bills === false ){
					

						Guest::addAsBillPayer(array(
											'guestId' => $payer->id,
											'roomId' => $beneficiaryRoom->id,
											'billTypes' => $pgBills
											));

					}else{
						# if true.. update
						Bill::updateBillPayer2($payer->id, $beneficiaryRoom->id, $pgBills);

					}

					# update bg's bill types
					Bill::updateBillPayer2($beneficiary->id, $beneficiaryRoom->id, $bgBills);


					Guest::changeIdInBillsTbl(array(
									'roomId' => $beneficiaryRoom->id,
									'newGuestId' => $payer->id,
									'startDate' => $sDate,
									'billTypes' => $sanitized['billType']
									));
					
		        }

		         #log Expenses transfer
		            $registry->get('logger')->logExpensesTransfer(array(
		            	'pg' => $payer->id,
		            	'bg' => $beneficiary->id,
		            	'pgRoom' => $payerRoom->id,
		            	'bgRoom' => $beneficiaryRoom->id,
		            	'billType' => $sanitized['billType']
		            	));

		         $this->execute(array('action'=>'display', 'tmpl' => 'transferExpenses', 'widget' => 'success', 'msg' => 'Guest Expenses for Guest ( ' . $beneficiary->name . ' ) staying in Room ( ' . $beneficiaryRoom->no .' ) was successfully transfered to Guest ( ' . $payer->name . ' ) staying in Room ( ' . $payerRoom->no . ' )' ));

			}

    }

    public function runAutoBilling()
    {
    	# code...
    	Guest::autoBill();
    }

    public function runAutoBilling2()
	{
		# code...
		if ( Guest::autoBill() ) {
			$this->execute(array( 'action' => 'display', 'tmpl' => 'manageGuestOptions', 'widget' => 'success', 'msg' => 'Autobilling Successfull' ));
		}else{
			$this->execute(array( 'action' => 'display', 'tmpl' => 'manageGuestOptions', 'widget' => 'error', 'msg' =>
					'Autobilling not Successfull...Please try After 1.0 Clock' ));
		}
	}

    public function exemptFromAutoBill(Array $data)
    {
    	# code...
    	global $registry;
    	$thisUser = unserialize($registry->get('session')->read('thisUser'));

		
		$requiredFields = array('roomId');
		# get all form fields into an array...
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}

		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			
			$this->execute(array('action'=>'display', 'tmpl' => 'autoBillExemption', 'widget' => 'error', 'msg' => $checkReq->msg));
		}
		
		$sanitized = array();
		foreach ($formFields as $key) {
			
			# code...
			$$key = $registry->get('form')->sanitize($_POST[$key], 'int');
			
			$sanitized[$key] = $$key ;

		}

		# check if time is up to 1pm
		if(time() >= strtotime("1pm")){

			$this->execute(array('action'=>'display', 'tmpl' => 'autoBillExemption', 'widget' => 'error', 'msg' => 'You cannot exempt Guest from Autobilling after 1pm'));

		}else{

			$guestInfo = Guest::getCheckInInfo($data['roomId']);
			$room = new Room($data['roomId']);



			# check if this guest is already exempted for today
			if(Room::isExemptedFromAutoBilling($data['roomId'], today())){

					$msg = $guestInfo->name . ' ( ' . $room->no . ' ) has already been added to Bill exemption List for Today  ( ' . dateToString(today()) . ' ) ';

					$this->execute(array('action'=>'display', 'tmpl' => 'autoBillExemption', 'widget' => 'info', 'msg' => $msg));
			}else{

					# add exemption
					Room::exemptFromAutoBill($data['roomId'], today(), $thisUser->id);

					

					# log bill Exemption addition
					$registry->get('logger')->logBillExemptionAddition(array(
								'roomNo' => $room->no,
								'guestName' => $guestInfo->name
								));

					

					$msg = $guestInfo->name . ' checked Into Room  ( ' . $room->no . ' ) will be exempted from Autobilling for today ( ' . dateToString(today()) . ' ) '; 

					$this->execute(array('action'=>'display', 'tmpl' => 'autoBillExemption', 'widget' => 'success', 'msg' => $msg));
			}

	 }
    }


    public function lateCheckOut(Array $data)
    {
    	# code...
    	global $registry;
    	$thisUser = unserialize($registry->get('session')->read('thisUser'));

		
		$requiredFields = array('roomId', 'time');
		# get all form fields into an array...
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}

		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			
			$this->execute(array('action'=>'display', 'tmpl' => 'lateCheckOut', 'widget' => 'error', 'msg' => $checkReq->msg));
		}
		
		$sanitized = array();
		foreach ($formFields as $key) {
			
			# code...
			$$key = $registry->get('form')->sanitize($_POST[$key], 'int');
			
			$sanitized[$key] = $$key ;

		}

		$guestInfo = Guest::getCheckInInfo($sanitized['roomId']);
		$room = new Room($sanitized['roomId']);


		# check if the room has already been added to exemption list
		if(Room::isExemptedFromAutoBilling($data['roomId'], today())){

			$msg = $guestInfo->name . ' ( ' . $room->no . ' ) has already been added to Bill exemption List for Today  ( ' . dateToString(today()) . ' ) so cannot be added to Late check Out List';

			$this->execute(array('action'=>'display', 'tmpl' => 'lateCheckOut', 'widget' => 'info', 'msg' => $msg));
			return;
		}

		# check if time is up to 1pm
		if(time() >= strtotime("1pm")){

			$this->execute(array('action'=>'display', 'tmpl' => 'lateCheckOut', 'widget' => 'error', 'msg' => 'late check out cannot be added after 1pm'));

		}else{

			


			# check if this guest is already exempted for today
			if(Room::inLateCheckOutList($sanitized['roomId'], today())){

					$msg = $guestInfo->name . ' ( ' . $room->no . ' ) also added to Late Checkout List for Today  ( ' . dateToString(today()) . ' ) ';

					$this->execute(array('action'=>'display', 'tmpl' => 'lateCheckOut', 'widget' => 'info', 'msg' => $msg));
			}else{

					#build late checkout time
					$time = strtotime('today ' . $sanitized['time']);

					# add late checkout
					Room::addToLateCheckOut($sanitized['roomId'], today(), $time, $thisUser->id);

					

					# log bill Exemption addition
					$registry->get('logger')->logLateCheckOutAddition(array(
								'roomNo' => $room->no,
								'guestName' => $guestInfo->name,
								'time' => $sanitized['time']
								));

					

					$msg = $guestInfo->name . ' checked Into Room  ( ' . $room->no . ' ) has been added into late Check Out List for today ( ' . dateToString(today()) . ' ) '; 

					$this->execute(array('action'=>'display', 'tmpl' => 'lateCheckOut', 'widget' => 'success', 'msg' => $msg));
			}

	 }
    }

   

	#end of class
}

