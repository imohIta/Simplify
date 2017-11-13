<?php

defined('ACCESS') or Error::exitApp();

class GuestController extends BaseController{
	
	protected $_urlAllowedMthds = array('render', 'checkInOptions', 'checkIn', 'complimentaryCheckIn', 'checkOut',  'printCheckInInvioce', 'fetchTransactions', 'showCheckOutOptions', 'transactions', 'viewTransactions', 'addBill', 'addPayment', 'changeRoom', 'manage', 'manageDiscount', 'transferExpenses', 'printGuestPaymentInvioce', 'autoBill', 'exemptFromAutoBill', 'autobillExemptions', 'lateCheckOut', 'autoBill2', 'flatRateCheckIn', 'guestBalances');
	

	public function render(){
	   $this->_model->attach(new GeneralView());
	   $this->_model->execute();
	}

	public function checkInOptions()
	{
		$this->_model->showCheckInOptions();
	}
	
	public function checkIn(){
		if(isset($_POST['submit'])){
			$this->_model->submitCheckIn($_POST);
		}else{
	   		$this->_model->showCheckInForm();
	    }
	}

	public function complimentaryCheckIn(){
		if(isset($_POST['submit'])){
			$this->_model->submitComCheckIn($_POST);
		}else{
	   		$this->_model->showComCheckInForm();
	    }
	}

	public function flatRateCheckIn(){
		if(isset($_POST['submit'])){
			$this->_model->submitFlatRateCheckIn($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'flatRateCheckIn', 'widget' => '', 'msg' => ''));
		}
	}
	
	public function checkOut()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->submitCheckOut($_POST);
		}else{
			$this->_model->showCheckOutForm();
		}
	}

	

	public function printCheckInInvioce()
	{
		# code...
		$this->_model->printCheckInInvioce();
	}

	public function printGuestPaymentInvioce()
	{
		# code...
		$this->_model->printGuestPaymentInvioce();
	}

	public function fetchTransactions()
	{
		# code...
		global $registry;
		$this->_model->fetchTransactions($registry->get('router')->getParam(0)[0]);

	}

	public function showCheckOutOptions()
	{
		# code...
		global $registry;
		$data = array('guestId' => $registry->get('router')->getParam(0)[0], 'totalBill' => $registry->get('router')->getParam(0)[1], 'totalPayment' => $registry->get('router')->getParam(0)[2], 'roomId' => $registry->get('router')->getParam(0)[3]);
		$this->_model->showCheckOutOptions($data);
	}


	public function transactions()
	{
		# code...
		$this->_model->showTransactionOptions();
	}

	public function viewTransactions()
	{
		# code...
		$this->_model->showTransaction();
	}
	
	public function addBill()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->submitAddGuestBill($_POST);
		}else{
			$this->_model->showAddBill();
		}
	}

	public function addPayment()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->submitAddGuestPayment($_POST);
		}else{
			$this->_model->showAddPayment();
		}
	}

	public function changeRoom()
	{
		if(isset($_POST['submit'])){
			$this->_model->submitChangeRoom($_POST);
		}else{
			$this->_model->showChangeRoomForm();
		}
	}

	

	public function manage()
	{
		# code...
		$this->_model->showManageGuestOptions();
	}

	public function manageDiscount()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->submitDiscountChange($_POST);
		}else{
			$this->_model->showmanageDiscount();
		}
	}

	

	public function transferExpenses()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->submitTransferExpenses($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'transferExpenses', 'widget' => '', 'msg' => ''));
		}
	}

	public function autoBill()
	{
		# code...
		$this->_model->runAutoBilling();
	}

	public function autoBill2()
	{
		# code...
		$this->_model->runAutoBilling2();
	}

	public function exemptFromAutoBill()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->exemptFromAutoBill($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'autoBillExemption', 'widget' => '', 'msg' => ''));
		}
	}

	public function autobillExemptions()
	{
		# code...
		$this->_model->execute(array('action'=>'render', 'tmpl' => 'autoBillExemptionsLog', 'widget' => '', 'msg' => ''));
	}

	public function lateCheckOut()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->lateCheckOut($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'lateCheckOut', 'widget' => '', 'msg' => ''));
		}
	}

	public function guestBalances(){
		$this->_model->execute(array('action'=>'render', 'tmpl' => 'fetchGuestBalances', 'widget' => '', 'msg' => ''));
	}




	# End of class
}
