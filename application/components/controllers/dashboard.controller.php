<?php

defined('ACCESS') or Error::exitApp();

class DashBoardController extends BaseController{
	
	protected $_urlAllowedMthds = array('render');
	
	
	public function render(){
	   #$this->_model->attach(new GeneralView());
	   $this->_model->execute();
	}
	
	public function limitedAccess(){
	   //echo '<br />This class is not supposed to be seen';	
	}
	
} 
