<?php
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class ReservationModel extends BaseModel{
	
	protected $_param;
	protected $_viewParams;

	/*
	* this function displays widgets ( err alerts, success alerts etc ) and renders pages ( check in, checkout etc )
	* param action : display ( in the case of a widget i.e the view will not load a new Page but will load the widgget on the tmpl value page passed )
	*				 render ( in the case of a tmpl, here the view will load the tmpl page without any widget) 
	* param msg : the message to be display on a widget...always empty if no widget is to be loaded
	*
	*/
	public function execute(Array $options = array('action'=>'render', 'tmpl' => 'newReservation', 'widget' => '', 'msg' => '')){
		$this->_viewParams = $options;
		$this->notify();
	} 

	public function makeReservation(Array $data)
	{
		
		# code...
		global $registry;
		$session = $registry->get('session');
		$thisUser = unserialize($session->read('thisUser'));

		$requiredFields = array('guestName', 'guestPhone', 'beginDate', 'endDate', 'payType');
		# get all form fields into an array...
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}

		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			$this->execute(array('action'=>'display', 'tmpl' => 'newReservation', 'widget' => 'error', 'msg' => $checkReq->msg));
		}

		#sanitize each of the fields & append to sanitized array
		$sanitized = array();
		foreach ($formFields as $key) {
			if($key != 'rooms2'){
				# code...
				if(in_array($key, array('amt1','amt2')) == true){
					$newAmt = amtToInt($_POST[$key]);
					$$key = $registry->get('form')->sanitize($newAmt, 'float');
				}else{
					$$key = $registry->get('form')->sanitize($_POST[$key], 'string');
				}
				$sanitized[$key] = $$key ;

		    }

		}
		

		if(strtotime($sanitized['endDate']) < strtotime(today())){
			$this->execute(array('action'=>'display', 'tmpl' => 'newReservation', 'widget' => 'error', 'msg' => 'Reservation End Date must not be earlier than today'));
		}

		if(strtotime($sanitized['endDate']) < strtotime($sanitized['beginDate'])){
			$this->execute(array('action'=>'display', 'tmpl' => 'newReservation', 'widget' => 'error', 'msg' => 'Reservation End Date must not be earlier than the Begin Date'));
		}

		# for each of the selected room...check availbality and add to db if availbale
		$resId = generateTransId();
		$msg = '<br />';
		$rooms = json_decode($session->read('rooms'), true);

		$rooms2 = array();
		
		foreach ($rooms as $key => $value) {
			# code...
			$sanitizedRoom = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
			//echo $sanitizedRoom . '<br />';

			$room = new Room($sanitizedRoom);
			$rooms2[$room->id] = $room->no;

			# if room is available
			if(Room::checkAvailablity($sanitizedRoom, $sanitized['beginDate'], $sanitized['endDate'])){

				$registry->get('db')->addReservation(array(
								'date' => $sanitized['date'],
								'startDate' => $sanitized['beginDate'],
								'endDate' => $sanitized['endDate'],
								'guestName' => $sanitized['guestName'],
								'guestPhone' => $sanitized['guestPhone'],
								'roomId' => $sanitizedRoom,
								'reserveId' => $resId
								));
				
				$msg .= $room->no . ' was Succesfully Reserved<br />';
			}else{
				$msg .=  '<span class="text-danger">' . $room->no . ' is Unavailable and was not reserved</span><br />';
			}
		}

		

		if($sanitized['amt1'] > 0){

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

			# insert into reservation payments
			$resPayId = $registry->get('db')->addReservationPayment(array(
								'date' => $sanitized['date'],
								'reserveId' => $resId,
								'amt' => $sanitized['amt1'],
								'details' => json_encode($det),
								'src' => 'app'
								));


			# insert into transactions
			Transaction::addNew(array(
								'date' => $sanitized['date'],
								'time' => now(),
								'transId' => $resId,
								'transType' => 10,
								'src' => json_encode(array('tbl' => 'reservationPayments', 'id' => $resPayId)),
								'details' => json_encode(array(
															'type' => 'Room Reservation Payment',
															'guestName' => $sanitized['guestName'],
															'desc' => json_encode($det),
															'amt' => $sanitized['amt1']
															)),
								'staffId' => $thisUser->id,
								'privilege' => $thisUser->get('activeAcct')
								));

		}

		# print Invioce

		$session->write('rooms', null);

		# Print Invioce
		$invioceData = array(
				'beginDate' => $sanitized['beginDate'],
				'endDate' => $sanitized['endDate'],
				'guestName' => $sanitized['guestName'],
				'guestPhone' => $sanitized['guestPhone'],
				'rooms2' => json_encode($rooms2),
				'revId' => $resId,
				'uri' => $registry->get('config')->get('baseUri') .'/reservation/',
				'amt' => $sanitized['amt1']
			);

		$session->write('showResInvioce', true);
	    $session->write('invioceType', 'Reservation');
	    $session->write('invioceData', $invioceData);
	    $registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/reservation/printInvioce');


		
	}

	public function checkRoomAvailablity(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$requiredFields = array('rooms', 'beginDate', 'endDate');
		# get all form fields into an array...
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}

		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			$this->execute(array('action'=>'display', 'tmpl' => 'newReservation', 'widget' => 'error', 'msg' => $checkReq->msg));
		}else{

				# check if end Date is earlier than today
			    $beginDate = filter_var($_POST['beginDate'], FILTER_SANITIZE_STRING);
				$endDate = filter_var($_POST['endDate'], FILTER_SANITIZE_STRING);
				
				if(strtotime($endDate) < strtotime(today())){
					$this->execute(array('action'=>'display', 'tmpl' => 'newReservation', 'widget' => 'error', 'msg' => 'Reservation End Date must not be earlier than today'));
				}elseif(strtotime($endDate) < strtotime($beginDate)){
					$this->execute(array('action'=>'display', 'tmpl' => 'newReservation', 'widget' => 'error', 'msg' => 'Reservation End Date must not be earlier than the Begin Date'));
				}else{

				# check if end date is earlier than start date

				$session->write('guestName', filter_var($_POST['guestName'], FILTER_SANITIZE_STRING));
				$session->write('guestPhone', filter_var($_POST['guestPhone'], FILTER_SANITIZE_STRING));
				$session->write('beginDate', filter_var($_POST['beginDate'], FILTER_SANITIZE_STRING));
				$session->write('endDate', filter_var($_POST['endDate'], FILTER_SANITIZE_STRING));
				$session->write('rooms', json_encode($_POST['rooms']));
				$session->write('showOtherOptions', true);
				
				$msg = '';
				$res = array();
				foreach ($_POST['rooms'] as $key => $value) {
					# code...
					$sanitizedRoom = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
					$res[$sanitizedRoom] = Room::checkAvailablity($sanitizedRoom, $beginDate, $endDate) ? 'yes' : 'no';
					echo 'test ' . $sanitizedRoom . '<br />';
				}


				foreach ($res as $key => $value) {
					# code...
					$room = new Room($key);
					$msg .= '<p>';
					$msg .= $room->no . ' : ';
					$msg .= $value == 'yes' ? '<span class="test-primary" style="padding-left:10px">Available</span>' : '<span class="text-danger" style="padding-left:10px">Unavailable</span> ';
					$msg .= '</p>';
				}
				

				$this->execute(array('action'=>'display', 'tmpl' => 'newReservation', 'widget' => 'info', 'msg' => $msg));
			    }

	  }

	}

	public function showViewOptions()
	{
		# code...
		$this->execute(array('action'=>'render', 'tmpl' => 'viewReservationOptions', 'widget' => '', 'msg' => ''));
	}

	public function showViewForm($src)
	{
		global $registry;
		
		$registry->get('session')->write('src', filter_var($src, FILTER_SANITIZE_STRING));

		$this->execute(array('action'=>'render', 'tmpl' => 'viewReservations', 'widget' => '', 'msg' => ''));
	}

	public function fetchByRevId($revId, $src, $return = false)
	{
		# code...
		global $registry;

		$data = $registry->get('db')->fetchReservedRoomsByResId($revId, $src);
		$rooms = array();
		$rooms2 = array();
		foreach ($data as $key) {
			$room = new Room($key->roomId);
			$rooms[] = $room->no;
			$rooms2[$room->id] = $room->no;
		}
		$res = array(
					'guestName' => $data[0]->guestName,
					'guestPhone' => $data[0]->guestPhone,
					'beginDate' => $data[0]->rStartDate,
					'endDate' => $data[0]->rEndDate,
					'rooms' => json_encode($rooms),
					'rooms2' => json_encode($rooms2)
					);
		if($return){
			return json_encode($res);
		}else{
			echo json_encode($res);
		}
	}

	public function showCheckInForm($revId, $src)
	{
		# code...
		global $registry;

		$data = json_decode($this->fetchByRevId($revId, $src, true));

		#fetch The guest Details
		$gDetails = Guest::fetchDetailsByPhone($data->guestPhone);
		if($gDetails !== false){
			$det = json_decode(json_encode($gDetails), true);
			$guest = new Guest($det);

			$msg = array(
					'guestId' => $guest->id,
					'name' => $guest->name,
					'phone' => $guest->phone,
					'occu' => $guest->occu,
					'nationality' => $guest->nationality,
					'addr' => $guest->addr,
					'reason' => $guest->reasonForVisit,
				);
		}else{
			$msg = array(
					'guestId' => '',
					'name' => $data->guestName,
					'phone' => $data->guestPhone,
					'occu' => '',
					'nationality' => '',
					'addr' => '',
					'reason' => '',
				);
		}

		$totalDeposit = 0;
		foreach ($this->fetchPaymentsByRevId($revId, true) as $row) {
			# code...
			$totalDeposit += $row->amt;
		}

		$msg['rooms'] = $data->rooms2;
		$msg['totalDeposit'] = $totalDeposit;
		$msg['revId'] = $revId;
		$msg['src'] = $src;

		$this->execute(array('action'=>'display', 'tmpl' => '', 'widget' => 'checkInFromReservation', 'msg' => $msg));

	}

	public function fetchPaymentsByRevId($revId, $return = false)
	{
		# code...
		global $registry;

		if($return){
			return $registry->get('db')->fetchReservationPayments($revId);
		}else{
			echo json_encode($registry->get('db')->fetchReservationPayments($revId));
		}
		
	}


	public function addPayment(Array $data)
	{ 
		# code...
		global $registry; 
		$session = $registry->get('session');
		$thisUser = unserialize($session->read('thisUser'));

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

			$msg = '<div class="alert alert-danger alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  <strong>Error!</strong>' . $checkReq->msg . 
				  '</div>';

			$session->write('formMsg', $msg);
			$registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/reservation/view/' . $data['src']);

			
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

		if($sanitized['amt1'] != $sanitized['amt2']){
			$msg = '<div class="alert alert-danger alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  <strong>Error!</strong>Amount and Confirm Amount Values must be the Same</div>';

			$session->write('formMsg', $checkReq->msg);
			$registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/reservation/view/' . $data['src']);
			
		}

		#if user chooses make payment option...
		if(isset($data['payType'])){

		    switch(strtolower($data['payType'])){
		    	case 'cash': case 'default' :
		    		$det = json_encode(array('Pay Type' => 'Cash'));
		    	break;

		    	case 'cheque':
		    		# code...
		    	    $det = json_encode(array('Pay Type' => 'Cheque', 'Bank' => $data['chequeBank'], 'Cheque No' => $data['chequeNo']));
		    		break;

		    	case 'pos':
		    		# code...
		    		$det = json_encode(array('Pay Type' => 'POS', 'POS No' => $data['posNo']));
		    		break;

		    	case 'bt':
		    		# code...
		    	    $det = json_encode(array('Pay Type' => 'Bank Transfer', 'Bank' => $data['btBank'], 'Transfer Date' => $data['btDate']));
		    		break;

		    }
		   
	    }else{ # if user did not choose a pay type...default to cash
	   		$det = json_encode(array('Pay Type' => 'Cash'));
	    } 


	    # Update Guest payment
		$resPayId = $registry->get('db')->addReservationPayment(array(
								'date' => today(),
								'reserveId' => $sanitized['revId'],
								'amt' => $sanitized['amt1'],
								'details' => $det,
								'src' => 'app'
								));


		# insert into transactions
		Transaction::addNew(array(
							'date' => today(),
							'time' => now(),
							'transId' => $sanitized['revId'],
							'transType' => 10,
							'src' => json_encode(array('tbl' => 'reservationPayments', 'id' => $resPayId)),
							'details' => json_encode(array(
														'type' => 'Room Reservation Payment',
														'guestName' => '',
														'desc' => $det,
														'amt' => $sanitized['amt1']
														)),
							'staffId' => $thisUser->id,
							'privilege' => $thisUser->get('activeAcct')
							));

	
	 # Print Invioce
	
	$invioceData =  json_decode($this->fetchByRevId($sanitized['revId'], $sanitized['src'], true), true);
	$invioceData['amt'] = $sanitized['amt1'];
	$invioceData['revId'] = $sanitized['revId'];
	$invioceData['uri'] = $registry->get('config')->get('baseUri') .'/reservation/view/' . $sanitized['src'];

	$session->write('showResInvioce', true);
    $session->write('invioceType', 'Reservation');
    $session->write('invioceData', $invioceData);
    $registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/reservation/printInvioce');


	}

	public function printInvioce()
	{
		# code...
		$this->execute(array('action'=>'render', 'tmpl' => 'reservationInvioce', 'widget' => '', 'msg' => ''));
	}


	public function editReservation(Array $data)
	{
		# code...
		global $registry; 
		$session = $registry->get('session');
		$thisUser = unserialize($session->read('thisUser'));

		$requiredFields = array('guestName', 'guestPhone', 'rooms', 'beginDate', 'endDate');
		
		# get all form fields into an array...
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}

		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){

			$msg = '<div class="alert alert-danger alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  <strong>Error!</strong>' . $checkReq->msg . 
				  '</div>';

			$session->write('formMsg', $msg);
			$registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/reservation/view/' . $data['src']);

			
		}

		
		#sanitize each of the fields & append to sanitized array
		$sanitized = array();
		foreach ($formFields as $key) {
			if($key != 'rooms'){
				$$key = $registry->get('form')->sanitize($_POST[$key], 'string');
				$sanitized[$key] = $$key ;
			}
		}

		if(strtotime($sanitized['endDate']) < strtotime(today())){
			$msg = '<div class="alert alert-danger alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  <strong>Error!</strong>Reservation End Date must not be earlier than today</div>';

			$session->write('formMsg', $msg);
			$registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/reservation/view/' . $sanitized['src']);

		}

		if(strtotime($sanitized['endDate']) < strtotime($sanitized['beginDate'])){
			$msg = '<div class="alert alert-danger alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  <strong>Error!</strong>Reservation End Date must not be earlier than the Begin Date</div>';

			$session->write('formMsg', $msg);
			$registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/reservation/view/' . $sanitized['src']);

		}

		# check rooms
		$rooms = explode(',', $data['rooms']);

		# select previous room
		$oldReservations = $registry->get('db')->fetchReservedRoomsByResId($sanitized['revId'], $sanitized['src']);
		$oldRooms = array();
		foreach ($oldReservations as $row) {
			# code...
			$oldRooms[] = $row->roomId;
		}

		# default no of available rooms to zero
		$available = 0;


		for ($i=0; $i < count($rooms); $i++) { 
			# code...
			$sanitizedRoom = filter_var($rooms[$i], FILTER_SANITIZE_STRING);
			$roomId = fetchRoomIdByNo(trim(strtoupper($sanitizedRoom)));
			
			# if the room exist
			if(!is_null($roomId)){

				# check if room is part of the old reserved rooms
				# true ? update : insert;
				# if 


				$mssg = '<br />';
				# if new room is not among the old rooms reserved...add new
				if(in_array($roomId, $oldRooms) === false){

						
						# if room is available
						if(Room::checkAvailablity($sanitizedRoom, $sanitized['beginDate'], $sanitized['endDate'])){

							$registry->get('db')->addReservation(array(
											'date' => $sanitized['date'],
											'startDate' => $sanitized['beginDate'],
											'endDate' => $sanitized['endDate'],
											'guestName' => $sanitized['guestName'],
											'guestPhone' => $sanitized['guestPhone'],
											'roomId' => $roomId,
											'reserveId' => $sanitized['revId']
											));
							$available++;
							$mssg .= $sanitizedRoom . ' was Succesfully Reserved<br />';
						}else{
							$mssg .=  '<span class="text-danger">' . $sanitizedRoom . ' is Unavailable and was not reserved</span><br />';
						}

				}/*else{
					# i shud probably check for room avalability before updating
					# it will possible give birth to some complications like...what if another person has booked for this
					# room for the new Date
					# Feeling pretty lazy right now...so ill postpone it


					# update room
					$registry->get('db')->updateReservation(array(
									'startDate' => $sanitized['beginDate'],
									'endDate' => $sanitized['endDate'],
									'guestName' => $sanitized['guestName'],
									'guestPhone' => $sanitized['guestPhone'],
									'roomId' => $roomId,
									'reserveId' => $sanitized['revId'],
									'src' => $sanitized['src']
									));

				}*/

				

			}



		}

		
		# if any new room was succesfully reserved...delete all old Rooms that are not also part of new room
		//if($available > 0){
			for ($i=0; $i < count($oldRooms) ; $i++) { 
				# code...
				$r = new Room($oldRooms[$i]);
				if(in_array($r->no, $rooms) === false){
					# delete Romm reservation
					
					$registry->get('db')->deleteReservation(array(
								'roomId' => $oldRooms[$i],
								'reverseId' => $sanitized['revId'],
								'src' => $sanitized['src']
								));
				}
			}
		//}


		$msg = '<div class="alert alert-info alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			  <strong>Info!</strong> Reservation Edit Summary. ' . $mssg . ' 
			</div>';

		 $session->write('formMsg', $msg);
			
		 $registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/reservation/view/' . $sanitized['src']);

	}

	public function cancelReservation(Array $data)
	{
		# code...
		global $registry; 
		$session = $registry->get('session');

		$revId = filter_var($data['revId'], FILTER_SANITIZE_STRING);
		$src = filter_var($data['src'], FILTER_SANITIZE_STRING);

		$oldReservations = $registry->get('db')->fetchReservedRoomsByResId($revId, $src);
		
		$oldRooms = array();
		foreach ($oldReservations as $row) {
			# code...
			$oldRooms[] = $row->roomId;
		}

		$registry->get('db')->deleteReservationFull($revId);

		# log Reservation cancelation
		$registry->get('logger')->logReservationCancelation(array(
						'guestName' => $oldReservations[0]->guestName,
						'rooms' => $oldRooms,
						'startDate' => $oldReservations[0]->rStartDate,
						'endDate' => $oldReservations[0]->rEndDate
					));

		$msg = '<div class="alert alert-success alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			  <strong>Success!</strong> Reservation was successfully cancelled. 
			</div>';

	 $session->write('formMsg', $msg);
		
	 $registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/reservation/view/' . $src);

	}

	public function checkInFromReservation(Array $data)
	{
		
		global $registry; 
		$session = $registry->get('session');
		$thisUser = unserialize($session->read('thisUser'));



		$requiredFields = array('date', 'phone', 'name', 'addr', 'nationality', 'roomId', 'deposit1', 'deposit2');
		
		# get all form fields into an array...
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}

		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){

			$msg = '<div class="alert alert-danger alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  <strong>Error!</strong>' . $checkReq->msg . 
				  '</div>';

			$session->write('formMsg', $msg);
			$registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/reservation/view/' . $data['src']);

			
		}

		
		#sanitize each of the fields & append to sanitized array
		$sanitized = array();
		foreach ($formFields as $key) {
			# code...
			if(in_array($key, array('deposit1','deposit2', 'noOfOccupants', 'discount')) == true){
				if(in_array($key, array('deposit1','deposit2')) !== false){
					/*if(!isset($_POST[$key]) || !$_POST[$key]){
						$newAmt = 0;
					}else{
						$newAmt = amtToInt($_POST[$key]);
				    }*/
					$$key = $registry->get('form')->sanitize($_POST[$key], 'float');
				}else{
					$$key = $registry->get('form')->sanitize($_POST[$key], 'int');
				}
			}else{
				$$key = $registry->get('form')->sanitize($_POST[$key], 'string');
			}
			$sanitized[$key] = $$key ;

		}

		#check if deposits amts match
		if($sanitized['deposit2'] != $sanitized['deposit1']){

			$msg = '<div class="alert alert-danger alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			  <strong>Error!</strong> Deposit & Confirm Deposit Amounts must be the Same   
			</div>';

			 $session->write('formMsg', $msg);
				
			 $registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/reservation/view/' . $sanitized['src']);
		}

		# check if deposit amount is greater than the total reservation payment amount
		if($sanitized['deposit1'] > $sanitized['revDeposit']){

			$msg = '<div class="alert alert-danger alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			  <strong>Error!</strong> Deposit Amount must Not be Greater than Total Resevation Deposit Amount.   
			</div>';

			 $session->write('formMsg', $msg);
				
			 $registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/reservation/view/' . $sanitized['src']);
		}

		$room = new Room($sanitized['roomId']);
		

		# check if the room to be checked into is currently occupied
		if($room->status == 'Occupied'){

			$msg = '<div class="alert alert-danger alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			  <strong>Error!</strong> Guest cannot be Checked into Room ' . $room->no . ' because it is currently occupied... Change Guest Reservation Room or Check Out Currently Checked In Guest.   
			</div>';

			 $session->write('formMsg', $msg);
				
			 $registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/reservation/view/' . $sanitized['src']);
		}

		# check if room to be checked in to is Currently Bad
		if($room->status == 'Bad'){

			$msg = '<div class="alert alert-danger alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			  <strong>Error!</strong> Guest cannot be Checked into Room ' . $room->no . ' because it is currently Bad... Change Guest Reservation Room or remove Room ' . $room->no . ' from Bad Rooms List.   
			</div>';

			 $session->write('formMsg', $msg);
				
			 $registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/reservation/view/' . $sanitized['src']);
		}



		/* 
		- subtract deposit payment from total reservation payment and store the diff
		- delete all reservation payment from its table and from reservation tabl
		- insert diff into reservation payment and transactions...make sure to set pay type to oldPayment
		*/
		$diff = $sanitized['revDeposit'] - $sanitized['deposit1'];

		# if user reserved only one room...delete reservation fully
		if($sanitized['roomCount'] == 1){
			
			$registry->get('db')->deleteReservationFull($sanitized['revId']);
		
		}else{

			# delete reservartion
			$registry->get('db')->deleteReservation(array(
				'src' => $sanitized['src'],
				'roomId' => $room->id,
				'reverseId' => $sanitized['revId']
				));

			# delete Reservation payments
			$registry->get('db')->deleteReservationPayments($sanitized['revId']);

			if($diff != 0 && $diff > -1){
			
				#insert Diff as Reservationpayment
				$det = json_encode(array('Pay Type' => 'Balance After CheckIn'));

				$resPayId = $registry->get('db')->addReservationPayment(array(
										'date' => today(),
										'reserveId' => $sanitized['revId'],
										'amt' => $diff,
										'details' => $det,
										'src' => 'app'
										));


				# insert into transactions
				Transaction::addNew(array(
									'date' => today(),
									'time' => now(),
									'transId' => $sanitized['revId'],
									'transType' => 10,
									'src' => json_encode(array('tbl' => 'reservationPayments', 'id' => $resPayId)),
									'details' => json_encode(array(
																'type' => 'Room Reservation Payment',
																'guestName' => '',
																'desc' => $det,
																'amt' => $diff
																)),
									'staffId' => $thisUser->id,
									'privilege' => $thisUser->get('activeAcct')
									));

			}
			
		}

		if($room->id < 69){

			Guest::checkInFromReservation($sanitized);

			$msg = '<div class="alert alert-success alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  <strong>Success!</strong> Guest ( ' . $sanitized['name'] . ' ) was successfully checked into Room ' . $room->no . '   
				</div>';

		}else{
			$msg = '<div class="alert alert-success alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			  <strong>Success!</strong> Reservation for ' . $room->no . ' was successfully Used   
			</div>';
		}

		 $session->write('formMsg', $msg);
			
		 $registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/reservation/view/' . $sanitized['src']);



	}





	#end of class
}

