<?php

defined('ACCESS') or Error::exitApp();

class SalesController extends BaseController{
	
	protected $_urlAllowedMthds = array('render','unposted','addTemp', 'deleteTemp', 'addNew','fetchUnpostedSaleByTransId','deleteUnposted','getPostOptions', 'deleteIncompleteSaleById', 'addToIncomplete');
	
	
	public function render(){
	   $this->_model->attach(new GeneralView());
	   $this->_model->showSalesForm();
	}
	 
	public function unposted() 
	{
		# code... 
		global $registry;
		if(isset($_POST['submit'])){
			$this->_model->postSale($_POST);
		}else{
			$this->_model->showUnpostedSales();
		}
	}

	public function addTemp()
	{
		# code...
		global $registry;
		$this->_model->addTemp(json_decode($_POST['data'], true));
	}

	public function deleteTemp()
	{
		# code...
		global $registry;
		$this->_model->deleteTemp($_POST);
	}

	public function addNew()
	{
		# code...
		global $registry;
		$this->_model->addNew($_POST);
	}

	public function fetchUnpostedSaleByTransId()
	{
		# code...
		global $registry;
		$this->_model->fetchUnpostedSaleByTransId($registry->get('router')->getParam(0)[0]);
	}

	public function deleteUnposted()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->deleteUnposted($_POST);
		}
	}

	public function getPostOptions()
	{
		# code...
		global $registry;
		$msg = array(
				'guestType' => $registry->get('router')->getParam(0)[0],
				'roomNo' => $registry->get('router')->getParam(0)[1]
				);
		$this->_model->execute(array('action'=>'display', 'tmpl' => '', 'widget' => 'postSaleOptions', 'msg' => $msg));
	}

	public function deleteIncompleteSaleById(){
		global $registry;
		$this->_model->deleteIncompleteSaleById($registry->get('router')->getParam(0)[0]);
	}

	public function addToIncomplete(){
		global $registry;
		$this->_model->addToIncomplete($registry->get('router')->getParam(0)[0]);
	}
	
}
