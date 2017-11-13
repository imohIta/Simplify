<?php
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class ReportModel extends BaseModel{
	
	protected $_param;
	protected $_viewParams;
	
	public function execute(Array $options){
		$this->_viewParams = $options;
		$this->notify();
	}
	


	#end of class
}

