<?php
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class AuditorModel extends BaseModel{
	
	protected $_param;
	protected $_viewParams;
	
	public function execute(Array $options){
		$this->_viewParams = $options;
		$this->notify();
	}
  
	
	public function setFinancailReportParams(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$year = filter_var($data['year'], FILTER_SANITIZE_NUMBER_INT);
		$month = filter_var($data['month'], FILTER_SANITIZE_NUMBER_INT);

		$session->write('ftYear', $year);
		$session->write('ftMonth', $month);

		$registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/auditor/financialReport');
	}

	public function setImpressReportParams(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$year = filter_var($data['year'], FILTER_SANITIZE_NUMBER_INT);
		$month = filter_var($data['month'], FILTER_SANITIZE_NUMBER_INT);

		$session->write('irYear', $year);
		$session->write('irMonth', $month);

		$registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/auditor/impressAcctMgt');
	}

	public function setfinancialPositionParams(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$year = filter_var($data['year'], FILTER_SANITIZE_NUMBER_INT);
		$month = filter_var($data['month'], FILTER_SANITIZE_NUMBER_INT);

		$session->write('fpYear', $year);
		$session->write('fpMonth', $month);

		$registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/auditor/financialPosition');
	}
	


	#end of class
}

