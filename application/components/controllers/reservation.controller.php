<?php

defined('ACCESS') or Error::exitApp();

class ReservationController extends BaseController{
	
	protected $_urlAllowedMthds = array('render','view', 'viewOptions', 'fetchByRevId', 'fetchPaymentsByRevId', 'checkInFromReservation', 'printInvioce');
	

	public function render(){
	   
	   $this->_model->attach(new GeneralView());

	   if(isset($_POST['submit'])){
	   		
	   		$this->_model->makeReservation($_POST);

	   }elseif(isset($_POST['checkAvailablity'])){

	   		$this->_model->checkRoomAvailablity($_POST);

	   }else{
	   		$this->_model->execute();
	   }

	}


	public function viewOptions()
	{
		# code...
		$this->_model->showViewOptions();
	}

	public function view()
	{
		# code...
		global $registry;

		if(isset($_POST['editDetails'])){
			
			$this->_model->editReservation($_POST);

		}elseif (isset($_POST['addPayment'])) {
			
			$this->_model->addPayment($_POST);

		}elseif(isset($_POST['cancel'])){
			$this->_model->cancelReservation($_POST);
		}else{
			$this->_model->showViewForm($registry->get('router')->getParam(0)[0]);
		}


	}

	public function fetchByRevId()
	{
		# code...
		global $registry;
		$this->_model->fetchByRevId($registry->get('router')->getParam(0)[0], $registry->get('router')->getParam(0)[1]);
	}

	public function fetchPaymentsByRevId()
	{
		# code...
		global $registry;
		$this->_model->fetchPaymentsByRevId($registry->get('router')->getParam(0)[0]);
	}

	public function checkInFromReservation()
	{
		# code...
		global $registry;

		if(isset($_POST['submit'])){
			$this->_model->checkInFromReservation($_POST);
		}else{
			$this->_model->showCheckInForm($registry->get('router')->getParam(0)[0], $registry->get('router')->getParam(0)[1]);
		}
	}

	public function printInvioce()
	{
		# code...
		$this->_model->printInvioce();
	}

	


	# End of class
}
