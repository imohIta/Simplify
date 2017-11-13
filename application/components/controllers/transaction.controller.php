<?php

defined('ACCESS') or Error::exitApp();

class TransactionController extends BaseController{ 
	
	protected $_urlAllowedMthds = array('reverseAppli', 'getTransById', 'log', 'calculateReturns', 'setLogPrivilege', 'mgtOptions','reversals', 'showTransDetails', 'showTransDetails2');
	
	
	public function render(){
	   $this->_model->attach(new GeneralView());
	   $this->_model->execute();
	}

	public function log()
	{
		global $registry;
		if(isset($_POST['search'])){
			$this->_model->showLog($_POST);
		}else{
			$this->_model->showLog();
		}
	}  
 
	public function reverseAppli()
	{

		if(isset($_POST['submit'])){
			$this->_model->reverseAppli($_POST);
		}else{
			$this->_model->showReversalForm();
		}
	}

	public function getTransById()
	{
		# code...
		global $registry;
		$this->_model->getTransById($registry->get('router')->getParam(0)[0]);
	}

	public function showTransDetails()
	{
		# code...
		global $registry;
		$this->_model->showTransDetails($registry->get('router')->getParam(0)[0]);
	}

	public function showTransDetails2()
	{
		# code...
		global $registry;
		$this->_model->showTransDetails2($registry->get('router')->getParam(0)[0]);
	}

	public function calculateReturns()
	{
		# code...
		global $registry;
		$this->_model->calculateReturns($registry->get('router')->getParam(0)[0], $registry->get('router')->getParam(0)[1]);
	}
	

	/*
	* This function is used by mgt staff
	* it allow the mgt staff to be able to view other depts transactions
	* by setting thier privilege in a session

	* This session is label after the mgt staff User and his privilege so it does not conflict with other accounts
	* incase of privilege change
	*/
	public function setLogPrivilege()
	{
		# code...
		global $registry; 
		$this->_model->setLogPrivilege($registry->get('router')->getParam(0)[0]);
	}

	public function mgtOptions()
	{
		# code...
		$this->_model->execute(array('action'=>'render', 'tmpl' => 'mgtTransactions', 'widget' => '', 'msg' => ''));
	}

	public function reversals()
	{
		# code...
		if(isset($_POST['approve'])){
			$this->_model->reverse($_POST);
		}elseif(isset($_POST['decline'])){
			$this->_model->rejectReserval($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'approveReverseTransaction', 'widget' => '', 'msg' => ''));
		}
	}
	
	
	
}
