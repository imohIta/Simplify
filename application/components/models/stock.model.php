<?php
/**
*
* 
*/
defined('ACCESS') || Error::exitApp();

class StockModel extends BaseModel{
	
	protected $_param; 
	protected $_viewParams;
	
	public function execute(Array $options = array('action'=>'render', 'tmpl' => 'viewStock', 'widget' => '', 'msg' => '')){
		$this->_viewParams = $options;
		$this->notify();
	}

	public function removeItem($data)
	{
		# code...  
		global $registry;
		$session = $registry->get('session');
		$thisUser = unserialize($session->read('thisUser'));

		$required = array('item', 'qty', 'reason');

		$checkReq = json_decode($registry->get('form')->checkRequiredFields($required));

		#if some required fields where not filled
		if($checkReq->status == 'error'){

			$this->execute(array('action'=>'display', 'tmpl' => 'stockRemovalForm', 'widget' => 'error', 'msg' => $checkReq->msg));
		}

		$item = filter_var($_POST['item'], FILTER_SANITIZE_NUMBER_INT);
		$qty = filter_var($_POST['qty'], FILTER_SANITIZE_NUMBER_INT);
		$reason = filter_var($_POST['reason'], FILTER_SANITIZE_STRING);

		$item = new PosItem(new Item($item));

		if($item->qtyInStock < $qty){

			$this->execute(array('action'=>'display', 'tmpl' => 'stockRemovalForm', 'widget' => 'error', 'msg' => 'Quantity in Stock of ' . $item->name . ' is less than ' . $qty));
		}

		# Remove Item from Stock
		$registry->get('db')->removeStockItem(array(
			'itemId' => $item->id,
			'qty' => $qty,
			'tbl' => $thisUser->tbl,
			'reason' => $reason,
			'staffId' => $thisUser->id,
			'privilege' => $thisUser->get('activeAcct')
			));

		# log Stock Removal
		$registry->get('logger')->logStockRemoval(array(
			'itemId' => $item->id,
			'qty' => $qty,
			'reason' => $reason
			));

		$this->execute(array('action'=>'display', 'tmpl' => 'stockRemovalForm', 'widget' => 'success', 'msg' => $qty .  ' ' . $item->name . ' was successfully removed from Stock'));
		
	} 

	public function showRemoveItemForm()
	{
		# code...
		$this->execute(array('action'=>'render', 'tmpl' => 'stockRemovalForm', 'widget' => '', 'msg' => ''));
	}
	

	public function showReducedItems()
	{
		# code...
		$this->execute(array('action'=>'render', 'tmpl' => 'storeReducedItems', 'widget' => '', 'msg' => ''));
	}

	public function showKitchenIssueForm()
	{
		# code...
		$this->execute(array('action'=>'render', 'tmpl' => 'issueKitchenItem', 'widget' => '', 'msg' => ''));
	}

	public function issueKitchenItem(Array $data)
	{
		# code...
		global $registry;

		$itemId = filter_var($data['item'], FILTER_SANITIZE_NUMBER_INT);
		$priv = filter_var($data['priv'], FILTER_SANITIZE_NUMBER_INT);
		$qty = filter_var($data['qty'], FILTER_SANITIZE_NUMBER_INT);

		$kitchenItem = new PosItem(new Item($itemId));
		$deptItem = new PosItem(new Item($itemId), User::getTblByPrivilege($priv));

		$kitchenItem->reduceFromStock($qty);
		$deptItem->IncreaseStockQty($qty);

		# log Stock Issue
		$registry->get('logger')->logStockIssue(array(
									'itemId' => $itemId,
									'qty' => $qty,
									'priv' => User::getRole($priv)
									));

		$this->execute(array('action'=>'display', 'tmpl' => 'issueKitchenItem', 'widget' => 'success', 'msg' => 'Item Successfully issued to ' . User::getRole($priv)));

	}

	public function showConversionRates()
	{
		# code...
		$this->execute(array('action'=>'render', 'tmpl' => 'conversionRates', 'widget' => '', 'msg' => ''));

	}

	public function showAddToStoreForm()
	{
		# code...
		$this->execute(array('action'=>'render', 'tmpl' => 'addStockToStore', 'widget' => '', 'msg' => ''));

	}

	public function addToStore(Array $data)
	{
		# code...
	}

	public function addTemp(Array $data)
	{
		global $registry;
		$session = $registry->get('session');
		$thisUser = unserialize($session->read('thisUser'));


		$item = new Item($data['itemId']);
			
		$d = array(
			'itemId' => $item->id,
			'itemName' => $item->name,
			'price' => $data['price'],
			'qty' => $data['qty'],
			'amt' => $data['amt']
			);


		# if temp stock cache already exist
		if($session->read('tempStock' . $thisUser->id)){

				# loop tru temp sales to check if new item to be added already exist in temp sales
				$found = false;
				foreach ($session->read('tempStock' . $thisUser->id) as $key => $value) {
					
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
						$session->deleteAssoc('tempStock' . $thisUser->id, $key);
						
						$newTemp = ($session->read('tempStock' . $thisUser->id));
						$session->write('tempStock' . $thisUser->id, null);

						# write updated items to temp sale
						$count = 0;
						foreach ($newTemp as $key => $value) {
							# code...
						
							if($count == 0){
								$s = array(0 => $value);
								$session->write('tempStock' . $thisUser->id, $s);
							}else{
								$session->writeAssoc('tempStock' . $thisUser->id, $count, $value);
							}
							
							$count++;
						}


						$session->writeAssoc('tempStock' . $thisUser->id, '', $newR);
						$found = true;
					}

				}

				if(!$found){
					$count = count($session->read('tempStock' . $thisUser->id));
					$session->writeAssoc('tempStock' . $thisUser->id, $count, $d);
				}


			}else{
				$s = array(0 => $d);
				$session->write('tempStock' . $thisUser->id, $s);
			}

			# echo reply to js
			echo json_encode(array(
					'status' => 'success',
					'msg' => json_encode($session->read('tempStock' . $thisUser->id))
					));

		
	}


	public function deleteTemp(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');
		$thisUser = unserialize($session->read('thisUser'));

		$newTemp = ($session->read('tempStock' . $thisUser->id));
		$session->write('tempStock' . $thisUser->id, null);


		$count = 0;
		foreach ($newTemp as $key => $value) {
			# code...

			if($value['itemId'] != $data['itemId']){
		
				if($count == 0){
					$s = array(0 => $value);
					$session->write('tempStock' . $thisUser->id, $s);
				}else{
					$session->writeAssoc('tempStock' . $thisUser->id, $count, $value);
				}
				
				$count++;
		   }
		}

		$registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/stock/addToStore');
	}

	public function addStockPurchase()
	{
		# code...
		global $registry;
		$session = $registry->get('session');
		$thisUser = unserialize($session->read('thisUser'));

		$transId = generateTransId();

		$purchase = array();
		foreach ($session->read('tempStock' . $thisUser->id) as $key => $value) {

			# create a json object and insert into stock purchases
			$purchases[] = array(
								'itemId' => $value['itemId'],
								'price' => $value['price'],
								'qty' => $value['qty'],
								'amt' => $value['amt']
								);

		}

		# insert into database
		$registry->get('db')->addStockPurchase(array(
			'date' => today(),
			'purchases' => json_encode($purchases),
			'staffId' => $thisUser->id
			));

		# Log Stock Addition
		$registry->get('logger')->logPurchaserStockAddition();

		$session->write('tempStock' . $thisUser->id, null);
		
		# print Bill
		$this->execute(array('action'=>'display', 'tmpl' => 'addStockToStore', 'widget' => 'success', 'msg' => 'Stock Purchase was successfully Posted'));
	}


	public function fetchPOSClosing(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$date = filter_var($data['date'], FILTER_SANITIZE_STRING);
		$priv = filter_var($data['dept'], FILTER_SANITIZE_NUMBER_INT);

		if($date == ''){
			$this->execute(array('action'=>'display', 'tmpl' => 'posDailyClosingStock', 'widget' => 'error', 'msg' => 'Please enter Date'));
		}

		if($priv == ''){
			$this->execute(array('action'=>'display', 'tmpl' => 'posDailyClosingStock', 'widget' => 'error', 'msg' => 'Please select Depatment'));
		}

		$res = $registry->get('db')->fetchClosingStock(array(
			'date' => $date,
			'privilege' => $priv
			));

		if(!empty($res)){
		
			$staff = new User($res->staffId);
			$result = array();
			
			foreach (json_decode($res->stock, true) as $key => $value) {
				# code...
				$item = new Item($key);
				$result[] = array(
					'itemName' => $item->name,
					'qty' => $value
					);
			}

			$session->write('closingStk', serialize(array(
				'closedBy' => $staff->name,
				'dept' => User::getRole($res->privilege),
				'date' => $res->date,
				'stock' => $result
				)));


		}

		$this->execute(array('action'=>'render', 'tmpl' => 'posDailyClosingStock', 'widget' => '', 'msg' => ''));

	}

	public function setStockPrivilege($priv)
	{
		# code...
		global $registry;
		$session = $registry->get('session');
		$uri = $registry->get('uri');
		$thisUser = unserialize($session->read('thisUser'));

		$privilege = filter_var($priv, FILTER_SANITIZE_NUMBER_INT);
		$session->write('stockPrivilege' . $thisUser->id . $thisUser->get('activeAcct'), $privilege);

		$uri->redirect($registry->get('config')->get('baseUri') . '/stock/');
	}

	public function viewStockPurchaseDetails($id)
	{
		# code...
		global $registry;

		$data = $registry->get('db')->fetchStockPurchaseDetailsById($id);
		$this->execute(array('action'=>'display', 'tmpl' => '', 'widget' => 'stockPurchases', 'msg' => $data));
	}

	public function deleteStkAddition(Array $data)
	{
		# code...
		global $registry;

		$id = filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT);
		$registry->get('db')->deleteStockPurchase($id);

		$this->execute(array('action'=>'display', 'tmpl' => 'rejectedStockAdditions', 'widget' => 'success', 'msg' => 'Stock Purchase successfully deleted'));

	}

	public function rejectStockPurchase(Array $data)
	{
		# code...
		global $registry;
		

		$id = filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT);
		$registry->get('db')->rejectStockPurchase($id);

		$res = $registry->get('db')->fetchStockPurchaseDetailsById($id);

		# log Stock rejection
		$registry->get('logger')->logStockAdditionRejection($res->date);

		$this->execute(array('action'=>'display', 'tmpl' => 'unapprovedStoreAdditions', 'widget' => 'success', 'msg' => 'Stock Purchase successfully rejected'));

	}

	public function approveStockPurchase(Array $data)
	{
		# code...
		
		global $registry;

		$id = filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT);
		$res = $registry->get('db')->fetchStockPurchaseDetailsById($id);
		
		foreach (json_decode($res->purchase) as $row) {
		
			$item = new PosItem(new Item($row->itemId), 'store');
			$item->IncreaseStockQty($row->qty);

		}


		$registry->get('db')->approveStockPurchase($id);

		# log Stock approval
		$registry->get('logger')->logStockAdditionApproval($res->date);

		$this->execute(array('action'=>'display', 'tmpl' => 'unapprovedStoreAdditions', 'widget' => 'success', 'msg' => 'Stock Purchase successfully approved'));

	}

	public function addItemToDept(Array $data)
	{
		# code...
		global $registry;
		//var_dump($data); die;

		$required = array('item', 'dept');

		$checkReq = json_decode($registry->get('form')->checkRequiredFields($required));

		#if some required fields where not filled
		if($checkReq->status == 'error'){

			$this->execute(array('action'=>'display', 'tmpl' => 'addItemToDept', 'widget' => 'error', 'msg' => $checkReq->msg));
			return;
		}
		
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}

		$sanitized = array();
		foreach ($formFields as $key) {
			# code...
			if(in_array($key, array('price','qty')) == true){
				if($key == 'price'){
					$newAmt = amtToInt($_POST[$key]);
					$$key = $registry->get('form')->sanitize($newAmt, 'float');
				}else{
					$$key = $registry->get('form')->sanitize($_POST[$key], 'price');
				}
			}else{
				$$key = $registry->get('form')->sanitize($_POST[$key], 'string');
			}
			$sanitized[$key] = $$key ;

		}

		# check if item already exist in depts's stock
		if($sanitized['dept'] == 12 || $sanitized['dept'] == 15){
			$item = new Item($sanitized['item']);

			#check if item already exist in this tbl
			if(Item::checkIfAlreadyExistInTable($item->id, User::getTblByPrivilege($sanitized[ 'dept' ]))){


				$msg = $item->name . ' already exist in ' . User::getRole($sanitized[ 'dept' ]) . '\'s Stock...';

				$this->execute(array( 'action' => 'display', 'tmpl' => 'addItemToDept', 'widget' => 'error', 'msg' =>
						$msg ));
			}else{

				#insert into stock table
				Item::insert($item->id, $sanitized['qty'], User::getTblByPrivilege($sanitized[ 'dept' ]));

				$msg = 'New Item ( ' . $item->name . ' ) successfully added to ' . User::getRole($sanitized[ 'dept' ]) . '\'s Stock';

				$this->execute(array( 'action' => 'display', 'tmpl' => 'addItemToDept', 'widget' => 'success', 'msg' => $msg ));
			}



		}else {
			$posItem = new PosItem(new Item($sanitized[ 'item' ]), User::getTblByPrivilege($sanitized[ 'dept' ]));


			if ( !is_null($posItem->qtyInStock) ) {

				$qty = $sanitized[ 'qty' ] == '' ? 0 : $sanitized[ 'qty' ] + $posItem->qtyInStock;

				PosItem::updateDetail2(User::getTblByPrivilege($sanitized[ 'dept' ]), 'qtyInStock', $qty, $sanitized[ 'item' ]);

				if ( isset($sanitized[ 'price' ]) && $sanitized[ 'price' ] != 0 ) {
					//update price
					PosItem::updateDetail2(User::getTblByPrivilege($sanitized[ 'dept' ]), 'sellingPrice', $sanitized[ 'price' ], $sanitized[ 'item' ]);
				}

				$msg = $posItem->name . ' already exist in ' . User::getRole($sanitized[ 'dept' ]) . '\'s Stock...Qty in Stock was updated Instead';

				$this->execute(array( 'action' => 'display', 'tmpl' => 'addItemToDept', 'widget' => 'success', 'msg' => $msg ));

			}
			else {

				$qty = $sanitized[ 'qty' ] == '' ? 0 : $sanitized[ 'qty' ];

				# insert into tbl
				PosItem::AddNew(array(
						'item'  => $sanitized[ 'item' ],
						'qty'   => $qty,
						'price' => $sanitized[ 'price' ],
						'tbl'   => User::getTblByPrivilege($sanitized[ 'dept' ])
				));

				$item = new Item($sanitized[ 'item' ]);

				$msg = 'New Item ( ' . $item->name . ' ) successfully added to ' . User::getRole($sanitized[ 'dept' ]) . '\'s Stock';

				$this->execute(array( 'action' => 'display', 'tmpl' => 'addItemToDept', 'widget' => 'success', 'msg' => $msg ));
			}
		}
	}


	public function addMenuReductionItem($no)
	{
		# code...
		$this->execute(array('action'=>'display', 'tmpl' => '', 'widget' => 'reductionQtyField', 'msg' => $no));
	}



	public function deploymentOpeningStk(Array $data)
	{
		# code...
		global $registry;
		$thisUser = unserialize($registry->get('session')->read('thisUser'));

		$registry->get('db')->updateItemInStock(array(
			'table' => User::getTblByPrivilege($thisUser->get('activeAcct')),
			'item' => $data['item'],
			'qty' => $data['qty']
			));

		$item = new Item($data['item']);
		$this->execute(array('action'=>'display', 'tmpl' => 'deploymentOpeningStk', 'widget' => 'success', 'msg' => $item->name . ' successfully added to Stock'));


	}

	public function addKitchenItem(Array $data){
		global $registry;
		$thisUser = unserialize($registry->get('session')->read('thisUser'));

		$registry->get('db')->updateItemInStock2(array(
				'table' => 'kitchenStk',
				'item' => filter_var($data['item'], FILTER_SANITIZE_NUMBER_INT),
				'qty' => filter_var($data['qty'], FILTER_SANITIZE_NUMBER_INT)
		));

		$item = new Item($data['item']);
		$this->execute(array('action'=>'display', 'tmpl' => 'addKitchenItem', 'widget' => 'success', 'msg' =>
				$item->name . ' successfully added to Kitchen Stock'));
	}
	

	
	


	#end of class
}

