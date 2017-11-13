<?php
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class RequisitionModel extends BaseModel{
	
	protected $_param;
	protected $_viewParams;
	
	public function execute(Array $options = array('action'=>'render', 'tmpl' => 'viewStock', 'widget' => '', 'msg' => '')){
		$this->_viewParams = $options;
		$this->notify();
	} 

	public function requisite(Array $data) 
	{
		# code...
		global $registry;
		$session = $registry->get('session');
		$thisUser = unserialize($session->read('thisUser'));

		$required = array('item', 'qty');

		$checkReq = json_decode($registry->get('form')->checkRequiredFields($required));

		#if some required fields where not filled
		if($checkReq->status == 'error'){

			$this->execute(array('action'=>'display', 'tmpl' => 'requisitionForm', 'widget' => 'error', 'msg' => $checkReq->msg));
		}

		$itm = filter_var($_POST['item'], FILTER_SANITIZE_NUMBER_INT);
		$qty = filter_var($_POST['qty'], FILTER_SANITIZE_NUMBER_INT);

		$item = new PosItem(new Item($itm));
		
		# if unit is pool bar and item requisited is either (goatmeat, cowleg, snail, meatpie)
		
		if($thisUser->get('activeAcct') == 8 && in_array($item->id, array(36,35,39,230))){
		
		    $item2 = new PosItem(new Item($itm), $thisUser->tbl);
			
			# increase item in requisitor table
			$item2->increaseStockQty($data['qty']);

			# log Requisition issue
			$registry->get('logger')->logRequisitionIssue(array(
				'itemId' => $item2->id,
				'qty' => $data['qty'],
				'staffId' => $thisUser->id,
				'role' => $thisUser->role
				));
		
			$this->execute(array('action'=>'display', 'tmpl' => 'requisitionForm', 'widget' => 'success', 'msg' => 'Requisition Application was Successfull and item was directly added to stock'));
			
		}else{

			# Remove Item from Stock
			$registry->get('db')->addRequisition(array(
				'date' => today(),
				'time' => time(),
				'itemId' => $item->id,
				'qty' => $qty,
				'staffId' => $thisUser->id,
				'privilege' => $thisUser->get('activeAcct')
				));

			# log Stock Removal
			$registry->get('logger')->logRequisitionApplication(array(
				'itemId' => $item->id,
				'qty' => $qty
				));
				
				

			$this->execute(array('action'=>'display', 'tmpl' => 'requisitionForm', 'widget' => 'success', 'msg' => 'Requisition Application was Successfull'));
		
		}
		
	} 

	public function showRequisitionForm()
	{
		# code...
		$this->execute(array('action'=>'render', 'tmpl' => 'requisitionForm', 'widget' => '', 'msg' => ''));
	}

	public function showIssued($data = array())
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		if(!empty($data)){
			# set beginDate to first Day of the current month of the current year
			$beginDate = $data['year'] . '-' . $data['month'] . '-' . '01';

			$endDate = $data['year'] . '-' . $data['month'] . '-' . getMonthLastDate($data['month']);

			$session->write('beginDate', $beginDate);
			$session->write('endDate', $endDate);
			$session->write('month', $data['month']);
		}
		

		$this->execute(array('action'=>'render', 'tmpl' => 'issuedRequisitions', 'widget' => '', 'msg' => ''));
	}

	public function showUnIssued()
	{
		# code...
		$this->execute(array('action'=>'render', 'tmpl' => 'unIssuedRequisitions', 'widget' => '', 'msg' => ''));
	}

	public function issue(Array $data)
	{
		# code...
		global $registry;
		$db = $registry->get('db');
		//$thisUser = unserialize($registry->get('session'));

		# create pos item for Store
		$item1 = new PosItem(new Item($data['itemId']));

		# create pos item for requisitor table
		$item2 = new PosItem(new Item($data['itemId']), $data['tbl']);

		# check if Item requisited has up to the qty required
		if($item1->qtyInStock >= $data['qty']){ 

			# reduce item from store
			$item1->reduceFromStock($data['qty']);

			# increase item in requisitor table
			$item2->increaseStockQty($data['qty']);

			# log Requisition issue
			$registry->get('logger')->logRequisitionIssue(array(
				'itemId' => $data['itemId'],
				'qty' => $data['qty'],
				'staffId' => $data['staffId'],
				'role' => $data['role']
				));

			$db->updateRequisition($data['id']);

			$msg = $data['qty']  . ' ' . $item1->name . ' was successfully issued to ' . $data['role'];
			$this->execute(array('action'=>'display', 'tmpl' => 'unIssuedRequisitions', 'widget' => 'success', 'msg' => $msg));
		}else{
			$msg = $item1->name . ' could not be issued to ' . $data['role'] . ' because ' . $data['qty'] . ' ' . $item1->unit . ' is required and only ' . $item1->qtyInStock . ' ' . $item1->unit . ' is availble in Stock';
			$this->execute(array('action'=>'display', 'tmpl' => 'unIssuedRequisitions', 'widget' => 'error', 'msg' => $msg));
		}

	}

	public function cancel(Array $data)
	{
		# code...
		global $registry;
		$baseUri = $registry->get('config')->get('baseUri');

		$registry->get('db')->deleteRequisition($data['id']);

		#log requisition delete

		$registry->get('uri')->redirect($baseUri .'/requisition/unissued');
	}

	
	public function fetchRequisitionDetails($itemId)
	{
		# code...
		global $registry;
		
		$item = new Item($itemId);
		$reqs = PosItem::fetchIssuedRequisitionDetails($itemId);

		$msg = array('itemName' => $item->name, 'reqs' => $reqs);

		$this->execute(array('action'=>'display', 'tmpl' => '', 'widget' => 'requisitionDetails', 'msg' => $msg));

	}
	
	


	#end of class
}

