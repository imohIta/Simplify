<?php

defined('ACCESS') or Error::exitApp();

class RequisitionController extends BaseController{
	
	protected $_urlAllowedMthds = array('apply', 'issued', 'unissued', 'issue', 'log', 'cancel', 'fetchRequisitionDetails');
	
	
	public function render(){
	   $this->_model->attach(new GeneralView());
	   $this->_model->execute();
	}
	
	public function apply()
	{
		# code.. . 
		global $registry;
		if(isset($_POST['submit'])){
			$this->_model->requisite($_POST);
		}else{
			$this->_model->showRequisitionForm();
		}
		
	}

	public function issued()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->issue($_POST);
		}else{
			$this->_model->showIssued();
		}
	}

	public function unissued()
	{
		# code...
		if(isset($_POST['issue'])){
			$this->_model->issue($_POST);
		}else{
			$this->_model->showUnIssued();
		}
	}
	
	public function cancel()
	{
		# code...
		$this->_model->cancel($_POST);
	}

	

	public function log()
	{
		# code...
		$this->_model->execute(array('action'=>'render', 'tmpl' => 'requisitionsLog', 'widget' => '', 'msg' => ''));
	}

	public function fetchRequisitionDetails()
	{
		# code...
		global $registry;
		$this->_model->fetchRequisitionDetails($registry->get('router')->getParam(0)[0]);
	}

	
	
	  
}
 