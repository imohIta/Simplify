<?php
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class ItemModel extends BaseModel{

	protected $_param;
	protected $_viewParams;

	public function execute(Array $options){
		$this->_viewParams = $options;
		$this->notify();
	}


	public function addNewStockItem(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$requiredFields = array('type', 'unit', 'name');
		# get all form fields into an array...
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}

		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			$this->execute(array('action'=>'display', 'tmpl' => 'newStockItem', 'widget' => 'error', 'msg' => $checkReq->msg));
		}

		#sanitize each of the fields & append to sanitized array
		$sanitized = array();
		foreach ($formFields as $key) {
			# code...
			if($key == 'itemtType' || $key == 'unit'){
				$$key = $registry->get('form')->sanitize($_POST[$key], 'int');
			}else{
				$$key = $registry->get('form')->sanitize($_POST[$key], 'string');
			}
			$sanitized[$key] = $$key;

		}

		# check if item with the same name already exist

		if(!Item::checkIfAlreadyExist(ucwords($sanitized['name']))){

				Item::addNew(array(
						'type' => $sanitized['type'],
						'unit' => $sanitized['unit'],
						'name' => ucwords($sanitized['name'])
						));

				$this->execute(array('action'=>'display', 'tmpl' => 'newStockItem', 'widget' => 'success', 'msg' =>'New Item ( ' . ucwords($sanitized['name']) . ' ) Successfully Added'));

		}else{
			$this->execute(array('action'=>'display', 'tmpl' => 'newStockItem', 'widget' => 'error', 'msg' =>'Item ( ' . ucwords($sanitized['name']) . ' ) already Exist'));
		}


	}

	public function addNewMenuItem(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$requiredFields = array('type', 'price', 'name');
		$reductionNo = $_POST['reductionsNo'];

		# to be used for sanitization
		$array = array('type', 'price');

		for ($i=1; $i <= $reductionNo ; $i++) {
			# code...
			$requiredFields[] = 'item'.$i;
			$requiredFields[] = 'rQty'.$i;

			$array[] = 'rQty'.$i;
		}

		# get all form fields into an array...
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}

		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			$this->execute(array('action'=>'display', 'tmpl' => 'newStockItem', 'widget' => 'error', 'msg' => $checkReq->msg));
		}

		#sanitize each of the fields & append to sanitized array
		$sanitized = array();
		foreach ($formFields as $key) {
			# code...
			if(in_array($key, $array) !== false){
				$$key = $registry->get('form')->sanitize($_POST[$key], 'int');
			}else{
				$$key = $registry->get('form')->sanitize($_POST[$key], 'string');
			}
			$sanitized[$key] = $$key;

		}

		# compile reduction qty
		$reductions = array();
		for ($i=1; $i <= $reductionNo ; $i++) {
			# code...
			$item = new Item($sanitized['item'.$i]);
			$reductions[$item->id] = $sanitized['rQty'.$i];

		}


		# check if item already exist
		if(!Menu::checkIfAlreadyExist(ucwords($sanitized['name']))){

			# add New item
			Menu::addNew(array(
				'name' => $sanitized['name'],
				'typeId' => $sanitized['type'],
				'price' => $sanitized['price'],
				'reductions' => json_encode($reductions)
				));

			$this->execute(array('action'=>'display', 'tmpl' => 'newMenuItem', 'widget' => 'success', 'msg' =>'New Menu ( ' . ucwords($sanitized['name']) . ' ) Successfully Added'));

		}else{
			$this->execute(array('action'=>'display', 'tmpl' => 'newMenuItem', 'widget' => 'error', 'msg' =>'Menu ( ' . ucwords($sanitized['name']) . ' ) already Exist'));
		}

	}

	public function editStockItem(Array $data)
	{
		# code...
		global $registry;

		$requiredFields = array('type', 'unit', 'name');
		# get all form fields into an array...
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}

		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			$this->execute(array('action'=>'display', 'tmpl' => 'editStockItem', 'widget' => 'error', 'msg' => $checkReq->msg));
		}

		if(isset($data['deleteItem'])){

			$item = new Item($data[ 'id' ]);

			# delete stock Item
			Item::deleteStockItem($data['id']);

			$msg = 'Item ( ' . $item->name . ') was successfully Deleted';

		}else {

			#sanitize each of the fields & append to sanitized array
			$sanitized = array();
			foreach ( $formFields as $key ) {
				# code...
				if ( in_array($key, array( 'type', 'unit', 'pbPrice', 'mbPrice', 'rdPrice' )) !== false ) {
					{
					}
					if ( in_array($key, array( 'pbPrice', 'mbPrice', 'rdPrice' )) !== false ) {
						if ( !isset($_POST[ $key ]) || !$_POST[ $key ] ) {
							$newAmt = 0;
						}
						else {
							$newAmt = amtToInt($_POST[ $key ]);
						}
						$$key = $registry->get('form')->sanitize($newAmt, 'float');
					}
					else {
						$$key = $registry->get('form')->sanitize($_POST[ $key ], 'int');
					}
				}
				else {
					$$key = $registry->get('form')->sanitize($_POST[ $key ], 'string');
				}
				$sanitized[ $key ] = $$key;

			}

			$item = new Item($sanitized[ 'id' ]);

			if ( $item->name != $sanitized[ 'name' ] ) {
				//update Name
				Item::updateDetail('name', $sanitized[ 'name' ], $item->id);
			}
			$item = new Item($sanitized[ 'id' ]);


			if ( $item->unitId != $sanitized[ 'unit' ] ) {
				//update unitId
				Item::updateDetail('unitId', $sanitized[ 'unit' ], $item->id);
			}

			if ( $item->typeId != $sanitized[ 'type' ] ) {
				//update unitId
				Item::updateDetail('typeId', $sanitized[ 'type' ], $item->id);
			}

			# pool bar
			$poolBrItem = new PosItem($item, 'pool_barStk');
			if ( $poolBrItem->price != $sanitized[ 'pbPrice' ] ) {
				PosItem::updateDetail('pool_barStk', $sanitized[ 'pbPrice' ], $poolBrItem->id);
			}


			# main bar
			$mainBrItem = new PosItem($item, 'main_barStk');
			if ( $mainBrItem->price != $sanitized[ 'mbPrice' ] ) {
				PosItem::updateDetail('main_barStk', $sanitized[ 'mbPrice' ], $mainBrItem->id);
			}

			# resturant drinks
			$resBrItem = new PosItem($item, 'resturant_drinksStk');
			if ( $resBrItem->price != $sanitized[ 'rdPrice' ] ) {
				PosItem::updateDetail('resturant_drinksStk', $sanitized[ 'rdPrice' ], $resBrItem->id);
			}

			$item = new Item($sanitized[ 'id' ]);
			$msg = 'Item ( ' . $item->name . ') was successfully edited';
		}

		$this->execute(array('action'=>'display', 'tmpl' => 'editStockItem', 'widget' => 'success', 'msg' => $msg));

	}

	public function editMenuItem(Array $data)
	{
		# code.
		global $registry;
		//var_dump($data); die;

		if(isset($data['deleteMenuItem'])){

			# delete stock Item
			Item::deleteMenuItem($data['id']);

			$msg = 'Item ( ' . $data['name'] . ') was successfully Deleted';

		}else {


			$requiredFields = array( 'type', 'price', 'name' );
			$reductionNo = filter_var($_POST[ 'reductionsNo' ], FILTER_SANITIZE_NUMBER_INT);

			# to be used for sanitization
			$array = array( 'type', 'price' );

			if ( $reductionNo != 0 ) {
				for ( $i = 1; $i <= $reductionNo; $i++ ) {
					# code...
					$requiredFields[] = 'item' . $i;
					$requiredFields[] = 'rQty' . $i;

					$array[] = 'rQty' . $i;
				}
			}

			# get all form fields into an array...
			$formFields = array();
			foreach ( $data as $key => $value ) {
				# code...
				$formFields[] = $key;
			}

			$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));


			#if some required fields where not filled
			if ( $checkReq->status == 'error' ) {
				$this->execute(array( 'action' => 'display', 'tmpl' => 'editMenuItem', 'widget' => 'error', 'msg' => $checkReq->msg ));
			}

			#sanitize each of the fields & append to sanitized array
			$sanitized = array();
			foreach ( $formFields as $key ) {
				# code...
				if ( in_array($key, $array) !== false ) {
					$$key = $registry->get('form')->sanitize($_POST[ $key ], 'int');
				}
				else {
					$$key = $registry->get('form')->sanitize($_POST[ $key ], 'string');
				}
				$sanitized[ $key ] = $$key;

			}

			$menu = new Menu($sanitized[ 'id' ]);

			if ( $menu->name != $sanitized[ 'name' ] ) {
				# edit Name
				Menu::updateDetail('name', $sanitized[ 'name' ], $menu->id);
			}

			if ( $menu->typeId != $sanitized[ 'type' ] ) {
				# edit type
				Menu::updateDetail('typeId', $sanitized[ 'type' ], $menu->id);
			}

			if ( $menu->price != $sanitized[ 'price' ] ) {
				# edit Name
				Menu::updateDetail('price', $sanitized[ 'price' ], $menu->id);
			}

			# compile reduction qty
			if ( $reductionNo != 0 ) {
				$reductions = array();
				for ( $i = 1; $i <= $reductionNo; $i++ ) {
					# code...
					$item = new Item($sanitized[ 'item' . $i ]);
					$reductions[ $item->id ] = $sanitized[ 'rQty' . $i ];

				}

				# edit reduction qty
				Menu::updateDetail('reductions', json_encode($reductions), $menu->id);
			}

			$msg = 'Menu ( ' . ucwords($sanitized['name']) . ' ) was successfully edited';
		}

		$this->execute(array('action'=>'display', 'tmpl' => 'editMenuItem', 'widget' => 'success', 'msg' => $msg));



	}




	#end of class
}
