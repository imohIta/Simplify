<?php
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class ReceptionModel extends BaseModel{
	
	protected $_param;
	protected $_viewParams;
	
	public function execute(Array $options){
		$this->_viewParams = $options;
		$this->notify();
	}
 
	 

	public function addChairmanXpenses(Array $data)
	{
		global $registry;

		$requiredFields = array('date', 'desc', 'amt1', 'amt2');
		# get all form fields into an array...
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}
		
		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			$this->execute(array('action'=>'display', 'tmpl' => 'addChairmanXpenses', 'widget' => 'error', 'msg' => $checkReq->msg));
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
			$sanitized[$key] = $$key;

		}

		if($sanitized['amt2'] != $sanitized['amt1']){
			$this->execute(array('action'=>'display', 'tmpl' => 'addChairmanXpenses', 'widget' => 'error', 'msg' => 'Amount & Confirm Deposit Amounts must be the Same'));
		}

		$registry->get('db')->addChairmanXpenses(array(
			'date' => $sanitized['date'],
			'details' => $sanitized['desc'],
			'transId' => generateTransId(),
			'amt' => $sanitized['amt1']
			));


		$this->execute(array('action'=>'display', 'tmpl' => 'addChairmanXpenses', 'widget' => 'success', 'msg' => 'Chairman Expenses succesfully Added'));


	}

	public function showAddBadRoomForm()
	{
		# code...
		$this->execute(array('action'=>'render', 'tmpl' => 'addBadRoom', 'widget' => '', 'msg' => ''));
	}

	public function showManageBadRoomOptions()
	{
		# code...
		$this->execute(array('action'=>'render', 'tmpl' => 'manageBadRoomOptions', 'widget' => '', 'msg' => ''));
	}

	public function addBadRoom(Array $data)
	{
		# code...
		global $registry;

		$requiredFields = array('date', 'roomType', 'roomNo', 'reason');
		# get all form fields into an array...
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}
		
		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){

			$this->execute(array('action'=>'display', 'tmpl' => 'addBadRoom', 'widget' => 'error', 'msg' => $checkReq->msg));
		}

		#sanitize each of the fields & append to sanitized array
		$sanitized = array();
		foreach ($formFields as $key) {
			# code...
			if(in_array($key, array('roomNo')) !== false){
				$$key = $registry->get('form')->sanitize($_POST[$key], 'int');
			}else{
				$$key = $registry->get('form')->sanitize($_POST[$key], 'string');
			}
			$sanitized[$key] = $$key;

		}

		Room::addBad(array(
			'dateAdded' => $sanitized['date'],
			'roomId' => $sanitized['roomNo'],
			'reason' => $sanitized['reason']
			));

		$registry->get('logger')->logBadRoomAdded(array(
			'roomId' => $sanitized['roomNo'],
			'reason' => $sanitized['reason']
			));

		$room = new Room($sanitized['roomNo']);

		$this->execute(array('action'=>'display', 'tmpl' => 'addBadRoom', 'widget' => 'success', 'msg' => 'Room ' . $room->no .' successfully Added to Bad Rooms list'));
	}

	public function viewBadRooms()
	{
		# code...
		$this->execute(array('action'=>'render', 'tmpl' => 'viewAllBadRooms', 'widget' => '', 'msg' => ''));
	}

	public function removeBadRoom(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$roomId = filter_var($data['roomId'], FILTER_SANITIZE_NUMBER_INT);
		$room = new Room($roomId);
		Room::removeBad($roomId);
		$msg = $room->no . ' was successfully removed from Bad Rooms List';
		$session->write('formMsg', '<div class="alert alert-success alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			  <strong>Success!</strong> ' . $msg . '</div>');

		# log Bad Room Removal
		$registry->get('logger')->logBadRoomRemoval(array(
			'roomId' => $roomId
			));
		$registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/reception/viewBadRooms');
	}


	public function setChaimanExpensesDate(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$year = filter_var($data['year'], FILTER_SANITIZE_NUMBER_INT);
		$month = filter_var($data['month'], FILTER_SANITIZE_NUMBER_INT);

		$session->write('ceYear', $year);
		$session->write('ceMonth', $month);

		$registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/reception/chairmanExpensesLog');
	}
	

	


	#end of class
}

