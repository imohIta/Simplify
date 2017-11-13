<?php

defined('ACCESS') or Error::exitApp();

class LoginController extends BaseController{
	
	protected $_urlAllowedMthds = array('render', 'authenticate','options');
	
	
	public function render(){
	   $this->_model->attach(new GeneralView());
	   $this->_model->execute();
	}
	
	public function authenticate(){
	   $this->_model->authenticate($_POST);
	}
	public function options()
	{
		# code...
		
		$this->_model->showLoginOptions();
	}
	
}
