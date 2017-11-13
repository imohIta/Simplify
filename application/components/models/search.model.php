<?php
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class SearchModel extends BaseModel{

	protected $_param;
	protected $_viewParams;

	public function execute(Array $options){
		$this->_viewParams = $options;
		$this->notify();
	}

	public function getFreeRoomsByTypes($roomTypeId)
	{
		# code...
		global $registry;
		$data = Room::fetchFreeByType($roomTypeId);
		$this->execute(array('action'=>'display', 'tmpl' => '', 'widget' => 'roomsList', 'msg' => array('data' => $data, 'jsAction' => 'fetchRoomPrice')));
	}

	public function fetchGuestDetailsByPhone($phone)
	{
		# code...
		global $registry;
		$phn = filter_var($phone, FILTER_SANITIZE_STRING);
		$data = Guest::fetchDetailsByPhone($phn);
		
		if($data === false){
			echo json_encode(array('err' => true, 'errMsg' => 'No Information was found for this guest'));
		}else{
			echo json_encode(array('err'=>false, 'gId' => $data->id, 'gName' => $data->name, 'addr' => $data->addr, 'nationality' => $data->nationality, 'occu' => $data->occu, 'reason' => $data->reasonForVisit, 'discount' => $data->discount));
		}

	}

	public function getGuestOutstandingBal($phone)
	{
		# code...
		global $registry;
		$phn = filter_var($phone, FILTER_SANITIZE_STRING);
		$data = Guest::getOutstandingBal($phn);
		if($data === false){
			echo json_encode(array('err' => true));
		}else{
			echo json_encode(array('err'=>false, 'amt' => $data->amt));
		}
	}

	public function getGuestCheckInInfo($roomId)
	{
		# code...
		global $registry;
		$rId = $registry->get('form')->sanitize($roomId, 'int');
		$data = Guest::getCheckInInfo($rId);
		if($data !== null && $data !== false){
			$room = new Room($rId);
			echo json_encode(array('status' => 'success','gId' => $data->id, 'gName' => $data->name, 'gAddr' => $data->addr, 'gPhone' => $data->phone, 'checkInDate' => dateToString($data->checkInDate), 'discount' => $data->discount, 'roomType' => $room->type, 'roomNo' => $room->no, 'roomId' => $roomId));
		}else{
			echo json_encode(array('status' => 'error'));
		}

	}

	public function fetchRoomPrice($roomId)
	{
		# code...
		$room = new Room($roomId);
		echo $room->price;
	}

	public function getGuestBillTypes(Array $data)
	{
		global $registry;

		# Payer : the guest who's is giong to cover expensed for another room
		# beneficiary :  the guest whose expenses is to be covered by guestId2


		# fetch guest bill Types dat payer is already covering for beneficiary
		$check = Bill::getBillsCoveredByGuestForRoom($data['payer'], $data['beneficiaryRoom']);
		$billTypes = array();

		if($check !== false){
			$billTypes = json_decode($check['billTypes'], true);
		}

		$msg = array('payer' => $data['payer'], 'beneficiary' => $data['beneficiary'], 'payerRoom' => $data['payerRoom'], 'beneficiaryRoom' => $data['beneficiaryRoom'], 'billTypes' => $billTypes );
		$this->execute(array('action'=>'display', 'tmpl' => '', 'widget' => 'guestBillTypes', 'msg' => $msg));

	}

	public function getItemPrice($itemId)
	{
		# code...
		$item = new PosItem(new Item($itemId));
		echo json_encode(array('price' => $item->price, 'qtyInStock' => $item->qtyInStock));
	}

	public function getMenuPrice($menuId)
	{
		# code...
		$menu = new Menu($menuId);
		echo json_encode(array('price' => $menu->price));
	}

	public function fetchGuestNameByRoomId($roomId)
	{
		# code...
		$checkInInfo = Guest::getCheckInInfo($roomId);
		echo $checkInInfo->name;
	}

	public function fetchItemDetails($itemId)
	{
		# code...

		$item = new Item($itemId);
		$poolBarItem = new PosItem($item, 'pool_barStk');
		$mainBarItem = new PosItem($item, 'main_barStk');
		$resDrinksItem = new PosItem($item, 'resturant_drinksStk');

		echo json_encode(array(
		'id' => $item->id,
		'name' => $item->name,
		'unitId' => $item->unitId,
		'unit' => $item->unit,
		'typeId' => $item->typeId,
		'type' => $item->type,
		'poolBarPrice' => $poolBarItem->price,
		'mainBarPrice' => $mainBarItem->price,
		'resDrinksPrice' => $resDrinksItem->price
		));
	}

	public function fetchMenuDetails($menuId)
	{
		# code...
		$menu = new Menu($menuId);
		$res = array(
			'id' => $menu->id,
			'name' => $menu->name,
			'type' => $menu->type,
			'typeId' => $menu->typeId,
			'price' => $menu->price
		);
		foreach (json_decode($menu->reductions, true) as $key => $value) {
			# code...
			$item = new Item($key);
			$res['reductions'][] = array('item' => $item->name, 'qty' => $value);
		}
		//var_dump($res); die;
		echo json_encode($res);
	}




	#end of class
}
