<?php
/**
* 
*
*/
defined('ACCESS') || Error::exitApp();

class SalesModel extends BaseModel{
	
	protected $_param;
	protected $_viewParams;
	
	public function execute(Array $options = array('action'=>'render', 'tmpl' => 'posSalesForm', 'widget' => '', 'msg' => '')){
		$this->_viewParams = $options;
		$this->notify();
	}
	
	public function showSalesForm()
	{
		# code...
		global $registry;
		$thisUser = unserialize($registry->get('session')->read('thisUser'));
 
		if($thisUser->get('activeAcct') == 10){
			$tmpl = 'resturantSalesForm';
		}else{ 
			$tmpl = 'posSalesForm';
		}

		$this->execute(array('action'=>'render', 'tmpl' => $tmpl, 'widget' => '', 'msg' => ''));
	}

	public function addTemp(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');
		$thisUser = unserialize($session->read('thisUser'));

		if($thisUser->get('activeAcct') == 10){
			$this->_addTempMenuSale($data);
		}else{
			$this->_addTempItemSale($data);
		}
	}

	private function _addTempMenuSale(Array $data)
	{
		global $registry;
		$session = $registry->get('session');
		$thisUser = unserialize($session->read('thisUser'));


		$menu = new Menu($data['itemId']);

		$reductions = json_decode($menu->reductions, true);

		$ok = true;

		# if the qty demanded fro the item is less than the qty in stock
		foreach($reductions as $k => $v) {
			# k is the item Id ... $v is the reduction qty

			# if the item is a drink...check reduction qty from resturantDrinks

			$itm = new Item($k);
			$item = $itm->typeId == 4 ? new PosItem($itm, 'resturant_drinksStk')
									  : new PosItem($itm, 'kitchenStk') ;


			# if the reduction item's qty in stock is less than the reduction qty multiplied be the no of the menu item desired
			if($item->qtyInStock < ($v * $data['qty'])){
				$ok = false;
			}
		}

		if(!$ok){

			$msg = '<div class="alert alert-danger alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  <strong>Error!</strong> Quantity in Stock of ' . $menu->name . ' is not up to ' . $data['qty'] . '</div>';
			echo json_encode(array('status' => 'error', 'msg' => $msg));
		
		}else{

				$d = array(
						'itemId' => $menu->id,
						'itemName' => $menu->name,
						'price' => $data['price'],
						'qty' => $data['qty'],
						'amt' => $data['amt']
						);


					# if temp sale cache already exist
					if($session->read('tempSales' . $thisUser->id . $thisUser->get('activeAcct'))){

						# loop tru temp sales to check if new item to be added already exist in temp sales
						$found = false;
						foreach ($session->read('tempSales' . $thisUser->id . $thisUser->get('activeAcct')) as $key => $value) {
							
							# if item already exist...
							if($value['itemId'] == $menu->id){
								$newR = array(
										'itemId' => $menu->id,
										'itemName' => $menu->name,
										'price' => $data['price'],
										'qty' => $data['qty'] + $value['qty'],
										'amt' => $data['amt'] + $value['amt']
										);
								# delete the item from temp sales
								$session->deleteAssoc('tempSales' . $thisUser->id . $thisUser->get('activeAcct'), $key);
								
								$newTemp = ($session->read('tempSales' . $thisUser->id . $thisUser->get('activeAcct')));
								$session->write('tempSales' . $thisUser->id . $thisUser->get('activeAcct'), null);

								# write updated items to temp sale
								$count = 0;
								foreach ($newTemp as $key => $value) {
									# code...
								
									if($count == 0){
										$s = array(0 => $value);
										$session->write('tempSales' . $thisUser->id . $thisUser->get('activeAcct'), $s);
									}else{
										$session->writeAssoc('tempSales' . $thisUser->id . $thisUser->get('activeAcct'), $count, $value);
									}
									
									$count++;
								}


								$session->writeAssoc('tempSales' . $thisUser->id . $thisUser->get('activeAcct'), '', $newR);
								$found = true;
							}

						}

						if(!$found){
							$count = count($session->read('tempSales' . $thisUser->id . $thisUser->get('activeAcct')));
							$session->writeAssoc('tempSales' . $thisUser->id . $thisUser->get('activeAcct'), $count, $d);
						}


					}else{
						$s = array(0 => $d);
						$session->write('tempSales' . $thisUser->id . $thisUser->get('activeAcct'), $s);
					}

					# echo reply to js
					echo json_encode(array(
							'status' => 'success',
							'msg' => json_encode($session->read('tempSales' . $thisUser->id . $thisUser->get('activeAcct')))
							));

				}

	}

	private function _addTempItemSale(Array $data)
	{
		global $registry;
		$session = $registry->get('session');
		$thisUser = unserialize($session->read('thisUser'));


		$item = new Item($data['itemId']);

		# if qty in stock for the item is greater than or equal to the required item
		if($data['qtyInStock'] >= $data['qty']){
			
			$d = array(
				'itemId' => $item->id,
				'itemName' => $item->name,
				'price' => $data['price'],
				'qty' => $data['qty'],
				'amt' => $data['amt']
				);


			# if temp sale cache already exist
			if($session->read('tempSales' . $thisUser->id . $thisUser->get('activeAcct'))){

				# loop tru temp sales to check if new item to be added already exist in temp sales
				$found = false;
				foreach ($session->read('tempSales' . $thisUser->id . $thisUser->get('activeAcct')) as $key => $value) {
					
					# if item already exist...
					if($value['itemId'] == $item->id){
						$newR = array(
								'itemId' => $item->id,
								'itemName' => $item->name,
								'price' => $data['price'],
								'qty' => $data['qty'] + $value['qty'],
								'amt' => $data['amt'] + $value['amt']
								);
						# delete the item from temp sales
						$session->deleteAssoc('tempSales' . $thisUser->id . $thisUser->get('activeAcct'), $key);
						
						$newTemp = ($session->read('tempSales' . $thisUser->id . $thisUser->get('activeAcct')));
						$session->write('tempSales' . $thisUser->id . $thisUser->get('activeAcct'), null);

						# write updated items to temp sale
						$count = 0;
						foreach ($newTemp as $key => $value) {
							# code...
						
							if($count == 0){
								$s = array(0 => $value);
								$session->write('tempSales' . $thisUser->id . $thisUser->get('activeAcct'), $s);
							}else{
								$session->writeAssoc('tempSales' . $thisUser->id . $thisUser->get('activeAcct'), $count, $value);
							}
							
							$count++;
						}


						$session->writeAssoc('tempSales' . $thisUser->id . $thisUser->get('activeAcct'), '', $newR);
						$found = true;
					}

				}

				if(!$found){
					$count = count($session->read('tempSales' . $thisUser->id . $thisUser->get('activeAcct')));
					$session->writeAssoc('tempSales' . $thisUser->id . $thisUser->get('activeAcct'), $count, $d);
				}


			}else{
				$s = array(0 => $d);
				$session->write('tempSales' . $thisUser->id . $thisUser->get('activeAcct'), $s);
			}

			# echo reply to js
			echo json_encode(array(
					'status' => 'success',
					'msg' => json_encode($session->read('tempSales' . $thisUser->id . $thisUser->get('activeAcct')))
					));

		}else{
			$msg = '<div class="alert alert-danger alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  <strong>Error!</strong> Quantity in Stok of ' . $item->name . ' is not Up to ' . $data['qty'] . '</div>';
			echo json_encode(array('status' => 'error', 'msg' => $msg));
		}
	}

	public function deleteTemp(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');
		$thisUser = unserialize($session->read('thisUser'));

		$newTemp = ($session->read('tempSales' . $thisUser->id . $thisUser->get('activeAcct')));
		$session->write('tempSales' . $thisUser->id . $thisUser->get('activeAcct'), null);


		$count = 0;
		foreach ($newTemp as $key => $value) {
			# code...

			if($value['itemId'] != $data['itemId']){
		
				if($count == 0){
					$s = array(0 => $value);
					$session->write('tempSales' . $thisUser->id . $thisUser->get('activeAcct'), $s);
				}else{
					$session->writeAssoc('tempSales' . $thisUser->id . $thisUser->get('activeAcct'), $count, $value);
				}
				
				$count++;
		   }
		}

		$url = $session->read('incompleteSaleTransId_' . $thisUser->id . '_' . $thisUser->get('activeAcct'))
				? $registry->get('config')->get('baseUri') . '/sales/addToIncomplete/' . $session->read('incompleteSaleTransId_' . $thisUser->id . '_' . $thisUser->get('activeAcct'))
				: $registry->get('config')->get('baseUri') . '/sales/';

		$registry->get('uri')->redirect($url);
	}

	public function addNew(Array $data)
	{
		# code...

		global $registry;
		$session = $registry->get('session');
		$thisUser = unserialize($session->read('thisUser'));

		$roomId = 0;

		if($session->read('incompleteSaleTransId_' . $thisUser->id . '_' . $thisUser->get('activeAcct'))){
			#fetch guest Type and posible room Id from sales using the transId
			$details = $registry->get('db')->fetchSaleDetails($session->read('incompleteSaleTransId_' . $thisUser->id . '_' . $thisUser->get('activeAcct')));


			if(is_null($details)){

				# display error if guestType and roonId could not be fetched
				$this->execute(array('action'=>'display', 'tmpl' => 'posSalesForm2', 'widget' => '', 'msg' =>
						'Operation not Successfull...Please try again'));
				return;
			}
			$details = json_decode($details);
			$guestType = $details->guestType;
			$roomId = $details->roomId;



		}else {

			$guestType = filter_var($data[ 'guestType' ], FILTER_SANITIZE_NUMBER_INT);
			if ( $guestType == 1 ) {
				$roomId = filter_var($data[ 'roomId' ], FILTER_SANITIZE_STRING);
			}

		}


		if($thisUser->get('activeAcct') == 10){
			$this->_addMenuSale($guestType, $roomId);
		}else{
			$this->_addItemSale($guestType, $roomId);
		}

	}

	private function _addItemSale($guestType, $roomId)
	{
		# code...
		global $registry;
		$session = $registry->get('session');
		$thisUser = unserialize($session->read('thisUser'));

		$transId = ($session->read('incompleteSaleTransId_' . $thisUser->id . '_' . $thisUser->get('activeAcct')) )
				? $session->read('incompleteSaleTransId_' . $thisUser->id . '_' . $thisUser->get('activeAcct'))
				: generateTransId();

		$added = array();

		foreach ($session->read('tempSales' . $thisUser->id . $thisUser->get('activeAcct')) as $key => $value) {
			# code...
			$item = new PosItem(new Item($value['itemId']));

			# if the qty demanded fro the item is less than the qty in stock
			if($item->qtyInStock >= $value['qty']){

				$registry->get('db')->addUnpostedSale($value, $transId, $guestType, $roomId, "item");
				$item->reduceFromStock($value['qty']);
				$added[] = $value;
				
			}

		}
		
		$res = array(
				'guestType' => $guestType,
				'roomId' => $roomId,
				'items' => $added
				);


		$session->write('tempSales' . $thisUser->id . $thisUser->get('activeAcct') ,null);
		$session->write('showSalesBill', true);
		$session->write('saleDetails' . $thisUser->id . $thisUser->get('activeAcct'), $res);
		
		# print Bill
		$this->execute(array('action'=>'render', 'tmpl' => 'salesBill', 'widget' => '', 'msg' => ''));
	}

	private function _addMenuSale($guestType, $roomId)
	{
		global $registry;
		$session = $registry->get('session');
		$thisUser = unserialize($session->read('thisUser'));

		# code...
		$added = array();
		$transId = ($session->read('incompleteSaleTransId_' . $thisUser->id . '_' . $thisUser->get('activeAcct')) )
				? $session->read('incompleteSaleTransId_' . $thisUser->id . '_' . $thisUser->get('activeAcct'))
				: generateTransId();



		foreach ($session->read('tempSales' . $thisUser->id . $thisUser->get('activeAcct')) as $key => $value) {
			# code...
			$menu = new Menu($value['itemId']);
			$reductions = json_decode($menu->reductions, true);

			$isAdded = false;

			# if the qty demanded fro the item is less than the qty in stock
			foreach($reductions as $k => $v) {
					# k is the item Id ... $v is the reduction qty

					$itm = new Item($k);
					$item = $itm->typeId == 4 ? new PosItem($itm, 'resturant_drinksStk')
							: new PosItem($itm, 'kitchenStk') ;

					$type = "menu";
					if($itm->typeId == 4){
						$type = "item";
					}

					# if the reduction item's qty in stock is less than the reduction qty multiplied be the no of the menu item desired
					if($item->qtyInStock < ($v * $value['qty'])){
						# throw error
					   $msg = '<div class="alert alert-danger alert-dismissible" role="alert">
					   <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					   <strong>Error!</strong>' . $menu->name . 
					   ' cannot be sold because the qty in Stock for ' . $item->name . ' is not up to ' . ($v * $value['qty']) . '</div>';

						$session->write('formMsg', $msg);
						$registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/sales/');
					}else{


						$item->reduceFromStock($v * $value['qty']);

						if($isAdded === false){
							$registry->get('db')->addUnpostedSale($value, $transId, $guestType, $roomId, $type);
							$added[] = $value;
							$isAdded = true;
						}

					}

			}

			$res = array(
				'guestType' => $guestType,
				'roomId' => $roomId,
				'items' => $added
				);


		}
		$session->write('tempSales' . $thisUser->id . $thisUser->get('activeAcct') ,null);
		$session->write('showSalesBill', true);
		$session->write('saleDetails' . $thisUser->id . $thisUser->get('activeAcct'), $res);
		

		# print Bill
		$this->execute(array('action'=>'render', 'tmpl' => 'salesBill', 'widget' => '', 'msg' => ''));
	}

	public function showUnpostedSales()
	{
		# code...
		$this->execute(array('action'=>'render', 'tmpl' => 'unpostedSales', 'widget' => '', 'msg' => ''));
	}

	public function fetchUnpostedSaleByTransId($transId)
	{
		# code...
		global $registry;
		$res = array();
		$sales = $registry->get('db')->fetchUnpostedSaleByTransId($transId);
		foreach ($sales as $row) {
			# code...
			$item = ($row->object == 1) ? new Menu($row->objectId) : new Item($row->objectId);
			$res[] = array(
					'autoId' => $row->id,
					'itemName' => $item->name,
					'qty' => $row->qty,
					'price' => $row->price,
					'amt' => $row->price * $row->qty
					);
		}

		if(!is_array($sales)){
			$roomNo = '';
		}else {
			if ( $sales[ 0 ]->roomId == 0 ) {
				$roomNo = '';
			}
			else {
				$room = new Room($sales[ 0 ]->roomId);
				$roomNo = $room->no;
			}
		}

		$data = array(
				'guestType' => $sales[0]->guestType,
				'roomId' => $sales[0]->roomId,
				'roomNo' => $roomNo,
				'res' => $res
				);

		echo json_encode($data);

	}

	public function postSale(Array $salesData)
	{
		# code...
		global $registry;
		$db = $registry->get('db');
		$thisUser = unserialize($registry->get('session')->read('thisUser'));

		$transId = filter_var($salesData['transId'], FILTER_SANITIZE_STRING);
		$guestType = filter_var($salesData['guestType'], FILTER_SANITIZE_NUMBER_INT);
		$roomId = filter_var($salesData['roomId'], FILTER_SANITIZE_NUMBER_INT);
		$payType = filter_var($salesData['payType'], FILTER_SANITIZE_STRING);

		# guest type desc
		if($guestType == 1){
			$guestTypeDesc = 'In Guest';
			
			$data = Guest::getCheckInInfo($roomId);
			$room = new Room($roomId);

			$guest = new Guest($data->id);

			$guestId = $guest->id;
			$buyerName = $guest->name;
			$roomNo = $room->no;
			$debtorType = 3;

	    }elseif ($guestType == 2) {

	    	$guestTypeDesc = 'Out Guest';
	    	$guestId = 0;
	    	$buyerName = 'Anonymous';
	    	$roomNo = '';
	    	$debtorType = 2;
	    
	    }else{
	    	$guestTypeDesc = 'Staff';
	    	$buyerName = 'Anonymous';
	    	$guestId = 0;
	    	$roomNo = '';
	    	$debtorType = 1;
	    }

	    #buyer Name




		switch (strtolower($payType)) {
			
			case 'cash':
				# code...

				# post Sales Here
				$totalAmt = 0;
				$ids = '';
				$details = array();
				$desc = '';
				foreach ($db->fetchUnpostedSaleByTransId($transId) as $row) {
					# code...
					$totalAmt += ($row->price * $row->qty);
					$ids .= $row->id . ',';
					$details[] = array(
						'objectType' => $row->object,
						'objectId' => $row->objectId,
						'qty' => $row->qty,
						'price' => $row->price 
						);

					$item = ($row->object == 1) ? new Menu($row->objectId) : new Item($row->objectId);
					$desc .= $row->qty . ' ' . $item->name . ', ';
				}

				$desc = trim($desc, ', ') . ' ( ' . $thisUser->role . ' )';

				$salesDetails = array(
						'guestType' => $guestTypeDesc,
						'guestName' => $buyerName,
						'guestRoom' => $roomNo,
						'ItemsSold' => $details
						);


				# Add Sale transaction
				Transaction::addNew(array(
			                        'date' => today(), 
			                        'time' => time(), 
			                        'transId' => $transId, 
			                        'transType' => 3, 
			                        'src' => json_encode(array('tbl' => 'sales', 'id' => trim($ids, ','))), 
			                        'details' => json_encode(array(
											'type' => 'Cash Sale',
											'guestId' => $guestId,
											'desc' => $desc,
											'saleDetails' => json_encode($salesDetails),
											'amt' => $totalAmt)), 
									'staffId' => $thisUser->id, 
									'privilege' => $thisUser->get('activeAcct')
				));

				
				break;


			case 'pos' :

	
				
				$posNo = filter_var($salesData['posReceiptNo'], FILTER_SANITIZE_STRING);

				# post Sales Here
				$totalAmt = 0;
				$ids = '';
				$details = array();
				$desc = '';
				foreach ($db->fetchUnpostedSaleByTransId($transId) as $row) {
					# code...
					$totalAmt += ($row->price * $row->qty);
					$ids .= $row->id . ',';
					$details[] = array(
						'objectType' => $row->object,
						'objectId' => $row->objectId,
						'qty' => $row->qty,
						'price' => $row->price 
						);

					$item = ($row->object == 1) ? new Menu($row->objectId) : new Item($row->objectId);
					$desc .= $row->qty . ' ' . $item->name . ', ';
				}

				$desc = trim($desc, ', ') . ' ( ' . $thisUser->role . ' )';

				$salesDetails = array(
						'guestType' => $guestTypeDesc,
						'guestName' => $buyerName,
						'guestRoom' => $roomNo,
						'POSReceiptNo' => $posNo,
						'ItemsSold' => $details
						);


				# Add Sale transaction
				Transaction::addNew(array(
			                        'date' => today(), 
			                        'time' => time() , 
			                        'transId' => $transId, 
			                        'transType' => 17, 
			                        'src' => json_encode(array('tbl' => 'sales', 'id' => trim($ids, ','))), 
			                        'details' => json_encode(array(
											'type' => 'POS Sale',
											'guestId' => $guestId,
											'desc' => $desc,
											'saleDetails' => json_encode($salesDetails),
											'amt' => $totalAmt)), 
									'staffId' => $thisUser->id, 
									'privilege' => $thisUser->get('activeAcct')
									));

				
				break;

			case 'credit' :

				if($guestType == 2){
					
					$buyerName = filter_var($salesData['debtorName'], FILTER_SANITIZE_STRING);
				
				}elseif($guestType == 3){
					$s = filter_var($salesData['staffId'], FILTER_SANITIZE_NUMBER_INT);
			    	$staff = new Staff($s);
			    	$buyerName = $staff->name;
				}

				# post Sales Here
				$totalAmt = 0;
				$ids = '';
				$details = array();
				$desc = '';
				foreach ($db->fetchUnpostedSaleByTransId($transId) as $row) {
					# code...
					$totalAmt += ($row->price * $row->qty);
					$ids .= $row->id . ',';
					$details[] = array(
						'objectType' => $row->object,
						'objectId' => $row->objectId,
						'qty' => $row->qty,
						'price' => $row->price
						);

					$item = ($row->object == 1) ? new Menu($row->objectId) : new Item($row->objectId);
					$desc .= $row->qty . ' ' . $item->name . ', ';
				}

				$desc = trim($desc, ', ') . ' ( ' . $thisUser->role . ') ';

				$salesDetails = array(
						'guestType' => $guestTypeDesc,
						'guestName' => $buyerName,
						'guestRoom' => $roomNo,
						'salesDetails' => $details
						);

				# Add Sale transaction
				Transaction::addNew(array(
			                        'date' => today(), 
			                        'time' => time() , 
			                        'transId' => $transId, 
			                        'transType' => 4, 
			                        'src' => json_encode(array('tbl' => 'sales', 'id' => trim($ids, ','))), 
			                        'details' => json_encode(array(
											'type' => 'Credit Sale',
											'guestId' => $guestId,
											'desc' => $desc,
											'saleDetails' => json_encode($salesDetails),
											'amt' => $totalAmt)), 
									'staffId' => $thisUser->id, 
									'privilege' => $thisUser->get('activeAcct')
				));


				# Add Creditor
				$db->addDebtor(array(
					'date' => today(),
					'debtorName' => $buyerName,
					'debtorType' => $debtorType,
					'transId' => $transId,
					'details' => json_encode($details),
					'amt' => $totalAmt,
					'staffId' => $thisUser->id,
					'privilege' => $thisUser->get('activeAcct'),
					
					));

				if($guestType == 3){ # if buyer is staff
					
					# Add Staff Debt
					$registry->get('db')->addStaffCredit(array(
						'date' => today(),
						'transId' => $transId,
						'staffId' => $staff->id,
						'details' => json_encode($details),
						'seller' => $thisUser->id,
						'dept' => $thisUser->get('activeAcct'),
						'amt' => $totalAmt
						));
				
				}elseif($guestType == 1){ # if buyer is inGuest
					
					# post to bill
					//var_dump($guestId, $roomId); die;
					
					Guest::addBill(array(
								'date' => today(), 
								'guestId' => $guestId, 
								'roomId' => $roomId, 
								'transId' => $transId, 
								'amt' => $totalAmt, 
								'billType' => 2, 
								'details' => $desc,
								'salesDetails' => json_encode($salesDetails)
								));

				}


				break;
			
			case 'postbill':

				# dertermine bill type
				switch ($thisUser->get('activeAcct')) {
					case 8: # pool bar
						$billType = 3;
						break;

					case 9: # main bar
						$billType = 4;
						break;

					case 10: case 11: # resturant | resturantDrinks
						$billType = 5;
						break;
					
					default:
						# code...
						break;
				}

				# post Sales Here
				$totalAmt = 0;
				$ids = '';
				$details = array();
				$desc = '';
				foreach ($db->fetchUnpostedSaleByTransId($transId) as $row) {
					# code...
					$totalAmt += ($row->price * $row->qty);
					$ids .= $row->id . ',';
					$details[] = array(
						'objectType' => $row->object,
						'objectId' => $row->objectId,
						'qty' => $row->qty,
						'price' => $row->price
						);

					$item = ($row->object == 1) ? new Menu($row->objectId) : new Item($row->objectId);
					$desc .= $row->qty . ' ' . $item->name . ', ';
				}

				$desc = trim($desc, ', ') . ' ( ' . $thisUser->role . ' )';

				$salesDetails = array(
						'guestType' => $guestTypeDesc,
						'guestName' => $buyerName,
						'guestRoom' => $roomNo,
						'ItemsSold' => $details
						);

				# Add Bill to guest Bill
				Guest::addBill(array(
								'date' => today(), 
								'guestId' => $guestId, 
								'roomId' => $roomId,
								'transId' => $transId, 
								'amt' => $totalAmt, 
								'billType' => $billType, 
								'details' => $desc,
								'salesDetails' => json_encode($salesDetails)
								));

				break;
			
		}

		# update Sales to Posted
		$db->updateUnpostedSale($transId);

		$this->execute(array('action'=>'display', 'tmpl' => 'unpostedSales', 'widget' => 'success', 'msg' => 'Sales successfully Posted'));



	}

	public function deleteUnposted(Array $data)
	{
		# code...
		global $registry;
		$db = $registry->get('db');
		$thisUser = unserialize($registry->get('session')->read('thisUser'));

		$transId = filter_var($data['transId'], FILTER_SANITIZE_STRING);
		

		foreach ($db->fetchUnpostedSaleByTransId($transId) as $row) {
			
			if($row->object == 1){
				# menu
				$menu = new Menu($row->objectId);
				$reductions = json_decode($menu->reductions, true);

				# if the qty demanded fro the item is less than the qty in stock
				foreach($reductions as $k => $v) {
					# k is the item Id ... $v is the reduction qty
					$item = new PosItem(new Item($k), 'kitchenStk');
					$item->IncreaseStockQty($v * $row->qty);
				}

			}else{
				# case Item
				$item = new PosItem(new Item($row->objectId));
				$item->IncreaseStockQty($row->qty);

			}

		

		}

		# delete from sales tbl
		$db->deleteFromTableByTransId('sales', $transId);

		$msg = '<div class="alert alert-success alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			  <strong>Success!</strong> Sales successfully deleted. 
			</div>';

		$registry->get('session')->write('formMsg', $msg);

		$registry->get('uri')->redirect($registry->get('config')->get('baseUri') .'/sales/unposted');

	
	}


	public function deleteIncompleteSaleById($id){
		global $registry;
		$registry->get('db')->deleteUnpostedSaleById($id);

	}


	public function addToIncomplete($transId)
	{
		# code...
		global $registry;
		$session = $registry->get('session');
		$thisUser = unserialize($registry->get('session')->read('thisUser'));

		$session->write('incompleteSaleTransId_' . $thisUser->id . '_' . $thisUser->get('activeAcct'), $transId);

		if($thisUser->get('activeAcct') == 10){
			$tmpl = 'resturantSalesForm2';
		}else{
			$tmpl = 'posSalesForm2';
		}

		$this->execute(array('action'=>'render', 'tmpl' => $tmpl, 'widget' => '', 'msg' => ''));
	}


	
	


	#end of class
}

