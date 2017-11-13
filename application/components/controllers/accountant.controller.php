<?php

defined('ACCESS') or Error::exitApp();

class AccountantController extends BaseController{
	
	protected $_urlAllowedMthds = array('ledger','stockReview');
	 
	
	public function render(){
	   $this->_model->attach(new GeneralView());
	   $this->_model->execute();
	}

	public function ledger()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->setLedgerDate($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'ledger', 'widget' => '', 'msg' => ''));
		}
	}

	public function stockReview()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->printStockReview($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'stockReview', 'widget' => '', 'msg' => ''));
		}
	}
	
	
	
	
}
