<?php

defined('ACCESS') or Error::exitApp();

class AccountController extends BaseController{

	protected $_urlAllowedMthds = array('render', 'changePrivilege', 'viewAll', 'addNew', 'delete', 'changeDept');


	public function render(){
		global $registry;
		/*$newPriv = $registry->get('router')->get('params')[1];
	   	$this->_model->changePrivilege($newPriv);*/
	   	//$this->_model->execute();
	}

	public function changePrivilege(){
		global $registry;
		$newPriv = $registry->get('router')->get('params')[1];
	   	$this->_model->changePrivilege($newPriv);
	}

	public function viewAll()
	{

		$this->_model->execute(array('action'=>'render', 'tmpl' => 'viewAllUsers', 'widget' => '', 'msg' => ''));
	}

	public function addNew()
	{
		if(isset($_POST['submit'])){
			$this->_model->addNew($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'addNewUser', 'widget' => '', 'msg' => ''));
		}
	}

	public function delete()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->delete($_POST);
		}
	}

	public function changeDept()
	{
		# code...
		global $registry;
		$session = $registry->get('session');
		if(isset($_POST['submit'])){
			$this->_model->changeUserDept($_POST);
		}else{
			$id = $registry->get('router')->getParam(0)[0];
			$session->write('uId', $id);
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'changeUserDept', 'widget' => '', 'msg' => ''));
		}
	}



}
