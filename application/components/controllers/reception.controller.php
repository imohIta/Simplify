<?php

defined('ACCESS') or Error::exitApp();

class ReceptionController extends BaseController{
	
	protected $_urlAllowedMthds = array('chairmanExpenses','manageBadRooms', 'addBadRoom', 'viewBadRooms', 'removeBadRoom', 'chairmanExpensesLog', 'chairmanExpensesOptions');
	
	
	public function render(){
	   $this->_model->attach(new GeneralView());
	   $this->_model->execute();
	}

 
	public function chairmanExpenses()
	{

		if(isset($_POST['submit'])){
			$this->_model->addChairmanXpenses($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'addChairmanXpenses', 'widget' => '', 'msg' => ''));
		}
	}
	
	public function manageBadRooms(){

	   $this->_model->showManageBadRoomOptions();
	   
	}

	public function addBadRoom()
	{

		if(isset($_POST['submit'])){
			$this->_model->addBadRoom($_POST);
		}else{
			$this->_model->showAddBadRoomForm();
		}
	}

	public function viewBadRooms()
	{
		# code...
		$this->_model->viewBadRooms();
	}

	public function removeBadRoom()
	{
		# code...
		$this->_model->removeBadRoom($_POST);
	}

	public function chairmanExpensesLog()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->setChaimanExpensesDate($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'chairmanExpensesLog', 'widget' => '', 'msg' => ''));
		}
	}

	public function chairmanExpensesOptions()
	{
		# code...
		$this->_model->execute(array('action'=>'render', 'tmpl' => 'chairmanExpensesOptions', 'widget' => '', 'msg' => ''));
	}
	
	
	
	
}
