<?php

defined('ACCESS') or Error::exitApp();

class ImpressController extends BaseController{
	
	protected $_urlAllowedMthds = array('render', 'payIn','addExpenses', 'addNewCategory');
	
	
	public function render(){
	   $this->_model->attach(new GeneralView());
	   if(isset($_POST['search'])){
	   	   $this->_model->setLogDate($_POST);
	   }else{
	   	   $this->_model->execute(array('action'=>'render', 'tmpl' => 'impressLog', 'widget' => '', 'msg' => ''));
	   }
	}


	public function payIn()
	{

		if(isset($_POST['submit'])){
			$this->_model->addNewPayIn($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'addImpressPayIn', 'widget' => '', 'msg' => ''));
		}
	}

	public function addExpenses()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->addExpenses($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'addImpressExpenses', 'widget' => '', 'msg' => ''));
		}
	}

	public function addNewCategory()
	{

		if(isset($_POST['submit'])){
			$this->_model->addNewCategory($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'addImpressCategory', 'widget' => '', 'msg' => ''));
		}
	}

	

	
	
	
	
}
