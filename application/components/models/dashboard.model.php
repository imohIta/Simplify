<?php
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class DashBoardModel extends BaseModel{
	
	protected $_param;
	protected $_viewMethod;
	
	public function execute(){
		global $registry;

		$thisUser = unserialize($registry->get('session')->read('thisUser'));

		if(!$registry->get('session')->read('loggedIn')){
		    $registry->get('uri')->redirect();
		}
		

		if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(7)) ){
			$tmpl = 'receptionDashboard'; 
		}elseif($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(8,9,10,11)) ){
			$tmpl = 'POSDashboard';
			//$tmpl = '';
		}elseif($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5)) ){
			$tmpl = 'mgtDashboard';
		}elseif($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(13,14)) ){
			$tmpl = 'POSDashboard';
		}else{
			$tmpl = '';
		}

		$this->_viewParams = array('action'=>'render', 'tmpl' => $tmpl, 'msg' => '');
		$this->notify();
	}
	
}

