<?php
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class AccountModel extends BaseModel{

	protected $_param;
	protected $_viewParams;

	public function execute(Array $options){
		$this->_viewParams = $options;
		$this->notify();
	}

	public function changePrivilege($newPriv){
		global $registry;

		#user sessions
		if($registry->get('session')->read('thisUser')){
			$thisUser = unserialize($registry->get('session')->read('thisUser'));

	        //var_dump($thisUser); die;
	        #change Privilege
	        $thisUser->set('activeAcct', $newPriv);
	        #override user details
			$registry->get('session')->write('thisUser', serialize($thisUser));

			#if user is not loggd in...login the user
			if(!$registry->get('session')->read('loggedIn')){
				$registry->get('session')->write('loggedIn', true);
			}

			#redirect to dashboard
			$registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/dashboard');
		}else{
			#redirect to login
			$registry->get('uri')->redirect($registry->get('config')->get('baseUri'));
		}
	}


	public function addNew(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$requiredFields = array('name', 'privilege');
		# get all form fields into an array...
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}

		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			$this->execute(array('action'=>'display', 'tmpl' => 'addNewUser', 'widget' => 'error', 'msg' => $checkReq->msg));
		}

		#sanitize each of the fields & append to sanitized array
		$sanitized = array();
		foreach ($formFields as $key) {
			# code...
			if($key == 'privilege'){
				$$key = $registry->get('form')->sanitize($_POST[$key], 'int');
			}else{
				$$key = $registry->get('form')->sanitize($_POST[$key], 'string');
			}

			$sanitized[$key] = $$key;

		}

		
		if($_POST['username'] != "" && $_POST['pwd2'] != ""){
				
				$requiredFields = array('username', 'pwd', 'pwd2');
				$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

				#if some required fields where not filled
				if($checkReq->status == 'error'){
					$this->execute(array('action'=>'display', 'tmpl' => 'addNewUser', 'widget' => 'error', 'msg' => $checkReq->msg));
					return;
				}

				if(strtolower($sanitized['pwd']) != strtolower($sanitized['pwd2'])){
					# if password and confirm password r not the same
					$this->execute(array('action'=>'display', 'tmpl' => 'addNewUser', 'widget' => 'error', 'msg' => 'Password & Confirm Password must be the Same'));
					return;

				}
		}

		#insert into staff db
		$staffId = Staff::addNew(array(
				'name' => ucwords($sanitized['name']),
				'privilege' => $sanitized['privilege']
			));


		if($_POST['username'] != "" && $_POST['pwd2'] != ""){
				
				User::addNew(array(
						'staffId' => $staffId,
						//'name' => ucwords($sanitized['name']),
						'username' => $sanitized['username'],
						'pwd' => $registry->get('authenticator')->hashPassword($sanitized['pwd']),
						//'privilege' => $sanitized['privilege'],

						));
		}

		$msg = 'Staff Details for ' . ucwords($sanitized['name']) . ' successfully added';
		$this->execute(array('action'=>'display', 'tmpl' => 'addNewUser', 'widget' => 'success', 'msg' => $msg));

	}

	public function delete(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$userId = filter_var($data['userId'], FILTER_SANITIZE_NUMBER_INT);

		$user = new User(new Staff($userId));

		User::delete($user->staffId);

		$mssg = $user->name . '\'s Account was successfully Deleted';
		$msg = '<div class="alert alert-info alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			  <strong>Info!</strong> Reservation Edit Summary. ' . $mssg . '
			</div>';

		 $session->write('formMsg', $msg);

		 $registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/account/viewAll/');
	}

	public function changeUserDept(Array $data)
	{
		# code...
		global $registry;

		$id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);

		$user = new User(new Staff($id));

		$priv = filter_var($_POST['privilege'], FILTER_SANITIZE_NUMBER_INT);

		if($user->privilege != $priv){
			User::update('deptId', $priv, $user->staffId);
		}

		$msg = $user->name . '\'s privilege was successfully changed from ' . $user->role . ' to ' . User::getRole($priv);

		$this->execute(array('action'=>'display', 'tmpl' => 'changeUserDept', 'widget' => 'success', 'msg' => $msg));


	}



	#end of class
}
