<?php
/**
*
*
*/
defined('ACCESS') || AppError::exitApp();

class LogoutModel extends BaseModel{
	
	protected $_param;
	protected $_viewParams;
	
	public function execute(){
		global $registry;

		$thisUser = unserialize($registry->get('session')->read('thisUser'));

		#user sessions
		$registry->get('session')->write('loggedIn', null);
		$registry->get('session')->write('thisUser', null);

		# try to update shift times
		setShiftTimes();

		# delete from loggedInUsers
		$registry->get('db')->removeFromLoggedInUsers($thisUser->staffId);

		# destroy session
		$registry->get('session')->destroy();

		#redirect to login page
		$registry->get('uri')->redirect();
	}

	public function execute2(Array $options)
	{
		# code...
		$this->_viewParams = $options;
		$this->notify();
	}

	public function showPosOptions()
	{
		# code...
		global $registry;
		$thisUser = unserialize($registry->get('session')->read('thisUser'));


		# if user privilege is not pool bar, main bar , resturant or resutrant drinks...logout
		if(in_array($thisUser->get('activeAcct'), array(8,9,10,11)) === false){
			
			$this->execute();
		}

		# check if this user has any unposted Bills
		$unpostedSales = count($registry->get('db')->fetchUnpostedSales());
		
		if( $unpostedSales > 0){
			
			$this->showUnpostedSalesMsg($unpostedSales);
		
		}else{

			$this->closeDayAccount();
		}



	}


	public function showUnpostedSalesMsg($unpostedSales)
	{
		# code...
		global $registry;

		$registry->get('session')->write('upMsg', $unpostedSales);
		$this->execute2(array('action'=>'render', 'tmpl' => 'unpostedSalesLogoutMsg', 'widget' => '', 'msg' => ''));
	}

	public function closeDayAccount($data = '')
	{
		# code...
		if($data){
			global $registry;
			$session = $registry->get('session');
			$thisUser = unserialize($session->read('thisUser'));

			$tbl = $thisUser->get('activeAcct') == 10 ? 'resturant_drinksStk' : $thisUser->tbl;

			# take a snap shot of users stock table an save
			$stock = array();
			foreach ($registry->get('db')->fetchStockItems($tbl) as $row) {
				# code...
				$stock[$row->itemId] = $row->qtyInStock;

			}

			#close stock only if the shift time has reached or it execcedded

			#check if closing Stock was already Added
			if($registry->get('db')->closingStockAdded(today(), $thisUser->get('activeAcct'))){
				
				# update
				$registry->get('db')->updateClosingStockSnapShot(array(
						'date' => today(),
						'time' => time(),
						'staffId' => $thisUser->id,
						'privilege' => $thisUser->get('activeAcct'),
						'stock' => json_encode($stock)
						));
			}else{
				
				# insert New
				$registry->get('db')->addClosingStockSnapShot(array(
						'date' => today(),
						'time' => time(),
						'staffId' => $thisUser->id,
						'privilege' => $thisUser->get('activeAcct'),
						'stock' => json_encode($stock)
						));

		    }


			# logout
			$this->execute();

		}else{
			$this->execute2(array('action'=>'render', 'tmpl' => 'closeDayAcctMsg', 'widget' => '', 'msg' => ''));
		}
	}

	


	#end of class
}

