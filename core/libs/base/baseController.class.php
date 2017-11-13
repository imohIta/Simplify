<?php

defined('ACCESS') or Error::exitApp();

class BaseController extends FuniObject{
	
	//protected $_params = array();
	protected $_model;
	
	public function __construct(BaseModel $model){
		$this->_model = $model;	
	}
	
	public function execute($params){
		
		$mthd = $params['mthd'];
		$a = array_splice($params, 1);
		//$c = $a['params'];
		foreach($a as $key => $value){
			$this->_params[$key] = $value;	
		}
		//check if mthd called from url is one of the url-allowed-mthds 
		//var_dump($this->_urlAllowedMthds); die;
		foreach ($this->_urlAllowedMthds as $key => $value) {
			 if($mthd == strtolower($value) || $mthd == $value){
				  $this->$mthd();
				  return;
			 }
	    }
		Error::throwException('( Invalid URL ) Access Denied to Method ', '404');
		
		//not used becos php in_array performs a case sensitive search
		/*if(!in_array($mthd, $this->_urlAllowedMthds)){
			Error::throwException('Access Denied to Method'); //Replace msg with invalid url upon deployment		
		}
		$this->$mthd();
		*/
		
	}
	
}