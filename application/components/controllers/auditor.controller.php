<?php

defined('ACCESS') or Error::exitApp();

class AuditorController extends BaseController{
	
	protected $_urlAllowedMthds = array('financialReport', 'impressAcctMgt', 'financialPosition');
	
	
	public function render(){
	   $this->_model->attach(new GeneralView());
	   $this->_model->execute();
	}

 
	public function financialReport()
	{
		if(isset($_POST['submit'])){
			$this->_model->setFinancailReportParams($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'auditorFinancialReport', 'widget' => '', 'msg' => ''));
		}
	}

	public function impressAcctMgt()
	{
		if(isset($_POST['submit'])){
			$this->_model->setImpressReportParams($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'impressAcctMgtReport', 'widget' => '', 'msg' => ''));
		}
	}

	public function financialPosition()
	{
		if(isset($_POST['submit'])){
			$this->_model->setFinancialPositionParams($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'financialPosition', 'widget' => '', 'msg' => ''));
		}
	}
	
	
	
	
}
