<?php

defined('ACCESS') or Error::exitApp();

class CreditsController extends BaseController{

	protected $_urlAllowedMthds = array('render','makePayment', 'paymentsLog', 'staffCreditsLog', 'fetchByTransId',
			'subchargesOption', 'addSubcharge', 'viewSubcharges', 'viewShortages');


	public function render(){
	   $this->_model->attach(new GeneralView());
	   $this->_model->execute();
	}

	public function makePayment()
	{
		# code...
		global $registry;
		if(isset($_POST['submit'])){
			$this->_model->addCreditPayment($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'posCreditPaymentForm', 'widget' => '', 'msg' => ''));
		}
	}

	public function paymentsLog()
	{
		# code...
		$this->_model->execute(array('action'=>'render', 'tmpl' => 'creditsPaymentLog', 'widget' => '', 'msg' => ''));
	}

	public function staffCreditsLog()
	{
		# code...
		if(isset($_POST['search'])){
			$this->_model->setStaffCreditSearchParams($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'staffCredits', 'widget' => '', 'msg' => ''));
		}
	}

	public function fetchByTransId()
	{
		# code...
		global $registry;
		$this->_model->fetchByTransId($registry->get('router')->getParam(0)[0]);
	}

	public function subchargesOption()
	{
		# code...
		$this->_model->execute(array('action'=>'render', 'tmpl' => 'subchargesOptions', 'widget' => '', 'msg' => ''));
	}

	public function addSubcharge()
	{
		if (isset($_POST['submit'])) {
			$this->_model->addSubcharge($_POST);
		}else {
			$this->_model->execute(array('action' => 'render', 'tmpl' => 'addSubcharge', 'widget' => '', 'msg' => ''));
		}
	}

	public function viewSubcharges(){
        if(isset($_POST['search'])){
            $this->_model->setStaffSubchargeSearchParams($_POST);
        }else{
            $this->_model->execute(array('action'=>'render', 'tmpl' => 'viewSubcharges', 'widget' => '', 'msg' => ''));
        }
	}


    public function viewShortages(){
        if(isset($_POST['search'])){
            $this->_model->setStaffShortagesSearchParams($_POST);
        }else {
            $this->_model->execute(array( 'action' => 'render', 'tmpl' => 'viewShortages', 'widget' => '', 'msg' => '' ));
        }
    }





}
