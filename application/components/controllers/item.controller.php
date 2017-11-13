<?php

defined('ACCESS') or Error::exitApp();

class ItemController extends BaseController{

	protected $_urlAllowedMthds = array('addNewOptions', 'newStockItem', 'newMenuItem', 'editOptions', 'editStockItem', 'editMenuItem');


	public function render(){
	   $this->_model->attach(new GeneralView());
	   $this->_model->execute();
	}


	public function addNewOptions()
	{

		$this->_model->execute(array('action'=>'render', 'tmpl' => 'newItemOptions', 'widget' => '', 'msg' => ''));
	}

	public function newStockItem()
	{
		if(isset($_POST['submit'])){
			$this->_model->addNewStockItem($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'newStockItem', 'widget' => '', 'msg' => ''));
		}
	}

	public function newMenuItem()
	{
		if(isset($_POST['submit'])){
			$this->_model->addNewMenuItem($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'newMenuItem', 'widget' => '', 'msg' => ''));
		}
	}

	public function editOptions()
	{
		# code...
		$this->_model->execute(array('action'=>'render', 'tmpl' => 'editItemOptions', 'widget' => '', 'msg' => ''));
	}

	public function editStockItem()
	{
		if(isset($_POST['submit']) || isset($_POST['deleteItem'])){
			$this->_model->editStockItem($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'editStockItem', 'widget' => '', 'msg' => ''));
		}
	}

	public function editMenuItem()
	{
		if(isset($_POST['submit']) || isset($_POST['deleteMenuItem'])){
			$this->_model->editMenuItem($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'editMenuItem', 'widget' => '', 'msg' => ''));
		}
	}








}
