<?php
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class ImpressModel extends BaseModel{
	
	protected $_param;
	protected $_viewParams;
	
	public function execute(Array $options){
		$this->_viewParams = $options;
		$this->notify();
	}

	
	

	public function addNewPayIn(Array $data)
	{
		global $registry;

		$requiredFields = array('date', 'src', 'amt1', 'amt2');
		# get all form fields into an array...
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}
		
		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			$this->execute(array('action'=>'display', 'tmpl' => 'addImpressPayIn', 'widget' => 'error', 'msg' => $checkReq->msg));
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
			$this->execute(array('action'=>'display', 'tmpl' => 'addImpressPayIn', 'widget' => 'error', 'msg' => 'Amount & Confirm Deposit Amounts must be the Same'));
		}

		$impress = new Impress();
		
		$impress->addPayIn(array(
			'date' => $sanitized['date'],
			'src' => $sanitized['src'],
			'amt' => $sanitized['amt1']
			));


		$this->execute(array('action'=>'display', 'tmpl' => 'addImpressPayIn', 'widget' => 'success', 'msg' => 'Impress Pay-In succesfully Added'));


	}

	public function addNewCategory(Array $data)
	{
		# code...
		global $registry;

		$requiredFields = array('catName');
		# get all form fields into an array...
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}
		
		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			$this->execute(array('action'=>'display', 'tmpl' => 'addImpressCategory', 'widget' => 'error', 'msg' => $checkReq->msg));
		}

		#sanitize each of the fields & append to sanitized array
		$sanitized = array();
		foreach ($formFields as $key) {
			# code...
			$$key = $registry->get('form')->sanitize($_POST[$key], 'string');
			$sanitized[$key] = $$key;

		}
		
		Impress::addNewCategory($sanitized['catName']);


		$this->execute(array('action'=>'display', 'tmpl' => 'addImpressCategory', 'widget' => 'success', 'msg' => 'New Impress Category ( ' . $sanitized['catName'] . ' ) succesfully Added'));

	}

	public function addExpenses(Array $data)
	{
		# code...
		global $registry;

		$requiredFields = array('date', 'category', 'amt1', 'amt2');
		# get all form fields into an array...
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}
		
		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			$this->execute(array('action'=>'display', 'tmpl' => 'addImpressExpenses', 'widget' => 'error', 'msg' => $checkReq->msg));
		}

		#sanitize each of the fields & append to sanitized array
		$sanitized = array();
		foreach ($formFields as $key) {
			# code...
			if(in_array($key, array('amt1','amt2', 'category')) !== false){
				if(in_array($key, array('amt1','amt2')) !== false){
					$newAmt = amtToInt($_POST[$key]);
					$$key = $registry->get('form')->sanitize($newAmt, 'float');
				}else{
					$$key = $registry->get('form')->sanitize($_POST[$key], 'int');
				}
			}else{
				$$key = $registry->get('form')->sanitize($_POST[$key], 'string');
			}
			$sanitized[$key] = $$key;

		}

		if($sanitized['amt2'] != $sanitized['amt1']){
			$this->execute(array('action'=>'display', 'tmpl' => 'addImpressExpenses', 'widget' => 'error', 'msg' => 'Amount & Confirm Deposit Amounts must be the Same'));
		}

		$impress = new Impress();
	
		$impress->addExpenses(array(
			'date' => $sanitized['date'],
			'category' => $sanitized['category'],
			'details' => $sanitized['details'],
			'amt' => $sanitized['amt1']
			));


		$this->execute(array('action'=>'display', 'tmpl' => 'addImpressExpenses', 'widget' => 'success', 'msg' => 'Impress Expenditure succesfully Added'));
	}

	public function setLogDate(Array $data)
	{
		# code...
		global $registry;

		$date = filter_var($data['date'], FILTER_SANITIZE_STRING);
		$registry->get('session')->write('impressDate', $date);
		$this->execute(array('action'=>'render', 'tmpl' => 'impressLog', 'widget' => '', 'msg' => ''));

	}

	


	#end of class
}

