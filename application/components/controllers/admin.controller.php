<?php

defined('ACCESS') or Error::exitApp();

class AdminController extends BaseController{
	
	protected $_urlAllowedMthds = array('resetApp', 'flushTable', 'setShiftTimes');
	
 
	public function render(){
		$this->_model->attach(new GeneralView());
	   $this->_model->execute();
	}


	public function resetApp()
	{
		
		if(isset($_POST['submit'])){
			$this->_model->resetApp($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'resetApp', 'widget' => '', 'msg' => ''));
		}
	}

	public function flushTable()
	{
		if(isset($_POST['submit'])){
			$this->_model->flushTable($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'flushTable', 'widget' => '', 'msg' => ''));
		}
	}

	public function setShiftTimes()
	{
		# code...
		$this->_model->setShiftTimes();
	}

	
	
	
	
}
