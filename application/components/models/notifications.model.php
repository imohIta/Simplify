<?php
/**
*
* 
*/
defined('ACCESS') || Error::exitApp();

class NotificationsModel extends BaseModel{
	
	protected $_param;
	protected $_viewParams; 

	//private $resetPwd = '$2y$10$rQ5iENJOXSQhXTFu5BpjoucFEKm9We8wlECaP0H.bn4syITWNElZG';
	private $resetPwd = 'SYz@A+min0_0';

	public function execute(Array $options = array('action'=>'render', 'tmpl' => 'notificationsLog', 'widget' => '', 'msg' => '')){
		$this->_viewParams = $options;
		$this->notify();

		# SYz@A+min0_0
	}
	
	
	



	#end of class
}

