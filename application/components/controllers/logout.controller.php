<?php

defined('ACCESS') or Error::exitApp();

class LogoutController extends BaseController{
	
	protected $_urlAllowedMthds = array('render', 'posOptions', 'closeDayAccount');
	
	
	public function render(){

	   $this->_model->execute();
	}

	public function posOptions()
	{
		# code...
		$this->_model->showPosOptions();
	}

	public function closeDayAccount()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->closeDayAccount($_POST);
		}else{
			$this->_model->closeDayAccount();
		}
	}
	
}
