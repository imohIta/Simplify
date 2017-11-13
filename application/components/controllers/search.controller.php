<?php

defined('ACCESS') or Error::exitApp();

class SearchController extends BaseController{

	protected $_urlAllowedMthds = array('getFreeRoomsByTypes','fetchGuestDetailsByPhone','getGuestOutstandingBal', 'fetchRoomPrice', 'getGuestCheckInInfo', 'getGuestBillTypes', 'getItemPrice', 'getMenuPrice', 'fetchGuestNameByRoomId', 'fetchItemDetails', 'fetchMenuDetails');


	public function render(){
	   $this->_model->attach(new GeneralView());
	   $this->_model->execute();
	}


	public function getFreeRoomsByTypes()
	{
		global $registry;
		$this->_model->getFreeRoomsByTypes($registry->get('router')->getParam(0)[0]);
	}

	public function fetchGuestDetailsByPhone(){
	   global $registry;
	   $this->_model->fetchGuestDetailsByPhone($registry->get('router')->getParam(0)[0]);
	}

	public function getGuestOutstandingBal()
	{
		# code...
		global $registry;
		$this->_model->getGuestOutstandingBal($registry->get('router')->getParam(0)[0]);
	}

	public function fetchRoomPrice()
	{
		# code...
		global $registry;
		$this->_model->fetchRoomPrice($registry->get('router')->getParam(0)[0]);
	}

	public function getGuestCheckInInfo()
	{
		global $registry;
		$this->_model->getGuestCheckInInfo($registry->get('router')->getParam(0)[0]);
	}

	public function getGuestBillTypes()
	{
		# code...
		global $registry;
		$this->_model->getGuestBillTypes(array(
			'payer' => $registry->get('router')->getParam(0)[0],
			'beneficiary' => $registry->get('router')->getParam(0)[1],
			'payerRoom' => $registry->get('router')->getParam(0)[2],
			'beneficiaryRoom' => $registry->get('router')->getParam(0)[3]
			));
	}

	public function getItemPrice()
	{
		global $registry;
		$this->_model->getItemPrice($registry->get('router')->getParam(0)[0]);
	}

	public function getMenuPrice()
	{
		global $registry;
		$this->_model->getMenuPrice($registry->get('router')->getParam(0)[0]);
	}

	public function fetchGuestNameByRoomId()
	{
		# code...
		global $registry;
		$this->_model->fetchGuestNameByRoomId($registry->get('router')->getParam(0)[0]);
	}

	public function fetchItemDetails()
	{
		# code...
		global $registry;
		$this->_model->fetchItemDetails($registry->get('router')->getParam(0)[0]);
	}

	public function fetchMenuDetails()
	{
		# code...
		global $registry;
		$this->_model->fetchMenuDetails($registry->get('router')->getParam(0)[0]);
	}




}
