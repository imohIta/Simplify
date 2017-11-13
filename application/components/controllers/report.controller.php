<?php

defined('ACCESS') or Error::exitApp();

class ReportController extends BaseController{
	
	protected $_urlAllowedMthds = array('roomStatus','police', 'roomList');
	
	
	public function render(){
	   $this->_model->attach(new GeneralView());
	   $this->_model->execute();
	}
	
	public function roomStatus(){
	   $this->_model->execute(array('action'=>'render', 'tmpl' => 'roomStatus', 'widget' => '', 'msg' => ''));
	}
	
	public function police()
	{
		# code...
		$this->_model->execute(array('action'=>'render', 'tmpl' => 'policeReport', 'widget' => '', 'msg' => ''));
	}

	public function roomList()
	{
		# code...
		$this->_model->execute(array('action'=>'render', 'tmpl' => 'roomList', 'widget' => '', 'msg' => ''));
	}
	
	
}
