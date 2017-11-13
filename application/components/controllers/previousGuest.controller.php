<?php

defined('ACCESS') or Error::exitApp();

class PreviousGuestController extends BaseController{
	
	protected $_urlAllowedMthds = array('render','stayHistory','viewCredits', 'fetchCreditsAndPayments', 'creditPayment', 'smsNotification',  'emailNotification', 'fetchStayRecords', 'fetchCreditBal', 'viewAllCredits');
	

	public function render(){
	   $this->_model->attach(new GeneralView());
	   $this->_model->execute();
	}

	public function stayHistory()
	{
		# code...
		$this->_model->showStayHistory();
	}

	public function fetchStayRecords()
	{
		# code...
		global $registry;
		$this->_model->fetchStayRecord($registry->get('router')->getParam(0)[0]);
	}

	public function viewCredits()
	{
		# code...
		$this->_model->execute(array('action'=>'render', 'tmpl' => 'previousGuestCredits', 'widget' => '', 'msg' => ''));
	}

	public function fetchCreditsAndPayments()
	{
		# code...
		global $registry;
		$this->_model->fetchCreditsAndPayments($registry->get('router')->getParam(0)[0]);
	}

	public function creditPayment()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->submitCreditPayment($_POST);
		}else{
			$this->_model->showCreditPaymentForm();
		}
	}

	public function fetchCreditBal()
	{
		# code...
		global $registry;
		$this->_model->fetchCreditBal($registry->get('router')->getParam(0)[0]);
	}

	public function viewAllCredits()
	{
		# code...
		$this->_model->execute(array('action'=>'render', 'tmpl' => 'allGuestCredits', 'widget' => '', 'msg' => ''));
	}



	# End of class
}
