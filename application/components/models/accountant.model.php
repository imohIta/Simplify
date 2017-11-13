<?php
/**
*
* 
*/
defined('ACCESS') || Error::exitApp();

class AccountantModel extends BaseModel{
	
	protected $_param;
	protected $_viewParams;
	
	public function execute(Array $options){
		$this->_viewParams = $options;
		$this->notify();
	}

	
	public function setLedgerDate(Array $data)
	{
		# code...
		global $registry;

		$date = filter_var($data['date'], FILTER_SANITIZE_STRING);
		
		$registry->get('session')->write('ledgerDate', $date);
		
		$this->execute(array('action'=>'render', 'tmpl' => 'ledger', 'widget' => '', 'msg' => ''));

	}

	public function printStockReview(Array $data){

		global $registry;
		$session = $registry->get('session');

		$month = filter_var($data['month'], FILTER_SANITIZE_NUMBER_INT);
		$year = filter_var($data['year'], FILTER_SANITIZE_NUMBER_INT);

		$session->write('StockReviewYear', $year);
		$session->write('StockReviewMonth', $month);

		$this->execute(array('action'=>'render', 'tmpl' => 'printStockReview', 'widget' => '', 'msg' => ''));
	}

	


	#end of class
}

