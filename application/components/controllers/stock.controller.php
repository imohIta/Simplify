<?php

defined('ACCESS') or Error::exitApp(); 

class StockController extends BaseController{
	
	protected $_urlAllowedMthds = array('render','removeItem','reducedItems', 'issueKitchenItem','conversionRates', 'addToStore', 'addTemp','deleteTemp', 'addStockPurchase','posDailyClosingStock', 'mgtOptions', 'setStockPrivilege','recentStoreAdditions', 'viewStockPurchaseDetails', 'rejectedStoreAdditions', 'deleteStockAddition', 'approvePurchase', 'removals', 'addItemToDept', 'addMenuReductionItem', 'posOpeningStock', 'deploymentOpeningStk',
			'resturantOptions', 'addKitchenItem');
	
	 
	public function render(){
	   $this->_model->attach(new GeneralView());
	   $this->_model->execute();
	} 
	
	public function removeItem()
	{
		# code...
		global $registry;
		if(isset($_POST['submit'])){
			$this->_model->removeItem($_POST);
		}else{
			$this->_model->showRemoveItemForm();
		}
	}  

	public function reducedItems()
	{
		# code...
		$this->_model->showReducedItems();
	}

	public function issueKitchenItem()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->issueKitchenItem($_POST);
		}else{
			$this->_model->showKitchenIssueForm();
		}
	}

	public function conversionRates()
	{
		# code...
		$this->_model->showConversionRates();
	}

	public function addToStore()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->addToStore($_POST);
		}else{
			$this->_model->showAddToStoreForm();
		}
	}

	public function addTemp()
	{
		global $registry;
		$this->_model->addTemp(json_decode($_POST['data'], true));
	}

	public function deleteTemp()
	{
		global $registry;
		$this->_model->deleteTemp($_POST);
	}

	public function addStockPurchase()
	{
		# code...
		global $registry;
		$this->_model->addStockPurchase();
	}

	public function posDailyClosingStock()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->fetchPOSClosing($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'posDailyClosingStock', 'widget' => '', 'msg' => ''));
		}
	}

	public function posOpeningStock()
	{
		# code...
		$this->_model->execute(array('action'=>'render', 'tmpl' => 'posDailyOpeningStock', 'widget' => '', 'msg' => ''));
	}

	public function mgtOptions()
	{
		# code...
		$this->_model->execute(array('action'=>'render', 'tmpl' => 'mgtStockOptions', 'widget' => '', 'msg' => ''));
	}

	public function setStockPrivilege()
	{
		# code...
		global $registry;
		$this->_model->setStockPrivilege($registry->get('router')->getParam(0)[0]);
	}

	public function recentStoreAdditions()
	{
		# code...
		$this->_model->execute(array('action'=>'render', 'tmpl' => 'recentStoreAdditions', 'widget' => '', 'msg' => ''));
	}

	public function rejectedStoreAdditions()
	{
		# code...
		$this->_model->execute(array('action'=>'render', 'tmpl' => 'rejectedStoreAdditions', 'widget' => '', 'msg' => ''));
	}

	public function viewStockPurchaseDetails()
	{
		# code...
		global $registry;
		$this->_model->viewStockPurchaseDetails($registry->get('router')->getParam(0)[0]);
	}

	public function deleteStockAddition()
	{
		# code...
		$this->_model->deleteStkAddition($_POST);
	}

	public function approvePurchase()
	{
		# code...
		if(isset($_POST['approve'])){
			$this->_model->approveStockPurchase($_POST);
		}elseif(isset($_POST['reject'])){
			$this->_model->rejectStockPurchase($_POST);
		}else{
		   $this->_model->execute(array('action'=>'render', 'tmpl' => 'unapprovedStoreAdditions', 'widget' => '', 'msg' => ''));
		}
	}

	public function removals()
	{
		# code...
		$this->_model->execute(array('action'=>'render', 'tmpl' => 'stockRemovals', 'widget' => '', 'msg' => ''));
	}

	public function addItemToDept()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->addItemToDept($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'addItemToDept', 'widget' => '', 'msg' => ''));
		}

	}

	public function addMenuReductionItem()
	{
		# code...
		global $registry;
		$this->_model->addMenuReductionItem($registry->get('router')->getParam(0)[0]);
	}

	public function deploymentOpeningStk()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->deploymentOpeningStk($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'deploymentOpeningStk', 'widget' => '', 'msg' => ''));
		}
	}

	public function resturantOptions()
	{
		# code...
		$this->_model->execute(array('action'=>'render', 'tmpl' => 'resturantStockOptions', 'widget' => '', 'msg' =>
				''));
	}

	public function addKitchenItem(){
		# code...
		if(isset($_POST['submit'])){
			$this->_model->addKitchenItem($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'addKitchenItem', 'widget' => '', 'msg' => ''));
		}
	}
	

	
	 
}
