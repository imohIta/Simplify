<?php
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class LoginModel extends BaseModel{
	
	protected $_param;
	protected $_viewParams;
	
	public function execute(Array $options = array('action'=>'render', 'tmpl' => 'login', 'widget' => '', 'msg' => '')){
		$this->_viewParams = $options;
		$this->notify();
	}


	public function authenticate($values)
	{
		# code...
		global $registry;

		#check if all required fields where filled
		$check = json_decode($registry->get('form')->checkRequiredFields(array('username','password')));
		if($check->status == 'error'){
		

			//set tmpl value bcos i want it to display the widget with the original form 
			$this->execute(array('action'=>'display', 'tmpl' => 'login', 'widget' => 'error', 'msg' => $check->msg));
			return false;
		}

		//var_dump($values); die;

		$username = $registry->get('form')->sanitize($values['username'], 'string');
		$pwd = $registry->get('form')->sanitize($values['password'], 'string');
		

		$staffId = $registry->get('authenticator')->verifyPassword($username, $pwd);
		
		

		if(!$staffId){
			$this->execute(array('action'=>'display', 'tmpl' => 'login', 'widget' => 'error', 'msg' => 'Invalid Username or Password'));
			return false;
		}
		
		$thisUser = new User(new Staff($staffId));


		#set login status
		$registry->get('session')->write('thisUser', serialize($thisUser));
		$registry->get('session')->write('loggedIn', true);

		# log user login
		$registry->get('logger')->logUserLogin(array(
			'staffId' => $thisUser->staffId,
			'staffPrivilege' => $thisUser->privilege
		));
		
		
		
		//try to update shiftTimes
		setShiftTimes();
		
		
		#if user is admin or duty manager
		if(array_search($thisUser->privilege, array(1,4,10)) !== false){
		
			$registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/login/options');
		}
			
		$registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/dashboard');
		

	}
	
    public function showLoginOptions()
    {
    	# code...
		
    	$this->execute(array('action'=>'render', 'tmpl' => 'loginOptions', 'widget' => '', 'msg' => ''));
		return;
    }


	#end of class
}

