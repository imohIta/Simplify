<?php

defined('ACCESS') or Error::exitApp();

class CashierController extends BaseController{
	
	protected $_urlAllowedMthds = array('newBankDeposit', 'viewBankDeposits','cashBook', 'collectReturns', 'asAtOptions', 'viewAsAtLog', 'fetchDeptCreditPayments', 'asAtPayment', 'fetchDeptCreditBal', 'guestChart');
	
	
	public function render(){
	   $this->_model->attach(new GeneralView());
	   $this->_model->execute();
	}


	public function newBankDeposit()
	{ 
		if(isset($_POST['submit'])){
			$this->_model->addBankDeposit($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'bankDepositForm', 'widget' => '', 'msg' => ''));
		}
	}

	public function viewBankDeposits()
	{
		# code...
		$this->_model->execute(array('action'=>'render', 'tmpl' => 'bankDepositsLog', 'widget' => '', 'msg' => ''));
	}

	public function cashBook()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->setCashBookDate($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'cashBook', 'widget' => '', 'msg' => ''));
		}
	}

	public function collectReturns()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->collectReturns($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'collectPosReturns', 'widget' => '', 'msg' => ''));
		}
	}

	public function asAtOptions()
	{
		# code...
		$this->_model->execute(array('action'=>'render', 'tmpl' => 'asAtOptions', 'widget' => '', 'msg' => ''));
	}

	public function viewAsAtLog()
	{
		# code...
		$this->_model->execute(array('action'=>'render', 'tmpl' => 'asAtPayments', 'widget' => '', 'msg' => ''));
	}

	public function fetchDeptCreditPayments()
	{
		# code...
		global $registry;
		$this->_model->fetchDeptCreditPayments($registry->get('router')->getParam(0)[0],$registry->get('router')->getParam(0)[1],$registry->get('router')->getParam(0)[2]);
	}

	public function asAtPayment()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->addNewAsAtPaymemt($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'newAsAtPayment', 'widget' => '', 'msg' => ''));
		}
	}

	public function fetchDeptCreditBal()
	{
		# code...
		global $registry;
		$this->_model->fetchDeptCreditBal($registry->get('router')->getParam(0)[0],$registry->get('router')->getParam(0)[1]);
	}

	public function guestChart()
	{
		# code...
		$this->_model->execute(array('action'=>'render', 'tmpl' => 'guestChart', 'widget' => '', 'msg' => ''));
	}
	
	
	
	
	
}
