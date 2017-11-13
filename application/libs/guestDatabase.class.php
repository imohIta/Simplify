<?php
namespace application\libs;
use \PDO;
use core\libs\Database as Db;
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class GuestDatabase extends Db{

	public function fetchDetails($guestId)
	{
		#
		$st = $this->_driver->prepare('CALL sp_fetchGuestDetails(:userId)');
		$st->bindValue(':userId', $guestId, PDO::PARAM_INT);
		return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : null;
	}

	public function fetchBills($guestId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchGuestBills(:guestId)');
		$st->bindValue(':guestId', $guestId, PDO::PARAM_INT);
		return $st->execute() ? $st->fetchAll(PDO::FETCH_ASSOC) : null;
	}


	public function deleteBills($guestId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_deleteGuestBills(:guestId)');
		$st->bindValue(':guestId', $guestId,PDO::PARAM_INT);
		return $st->execute() ? true : false;
	}

	public function fetchTotalBill($guestId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchTotalGuestBill(:guestId)');
		$st->bindValue(':guestId', $guestId);
		$st->execute();
		$st->bindColumn('total', $total);
		return $st->fetch(PDO::FETCH_ASSOC) ? $total : null;
	}

	public function fetchPayments($guestId, $date)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchGuestPayments(:guestId)');
		$st->bindValue(':guestId', $guestId, PDO::PARAM_INT);
		return $st->execute() ? $st->fetchAll(PDO::FETCH_ASSOC) : null;
	}

	public function fetchPaymentsForDate($guestId, $date)
	{
		# code...
		$st = $this->_driver->prepare('select * from `guestPayments` where `date` = :date and `guestId` = :guestId');
		$st->bindValue(':date', $date, PDO::PARAM_STR);
		$st->bindValue(':guestId', $guestId, PDO::PARAM_INT);
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
	}

	public function deletePayments($guestId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_deleteGuestPayments(:guestId)');
		$st->bindValue(':guestId', $guestId, PDO::PARAM_INT);
		return $st->execute() ? true : false;
	}

	public function fetchTotalPayment($guestId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchTotalGuestPayment(:guestId)');
		$st->bindValue(':guestId', $guestId, PDO::PARAM_INT);
		$st->execute();
		$st->bindColumn('total', $total);
		return $st->fetch(PDO::FETCH_ASSOC) ? $total : null;
	}

	public function fetchTotalPaymentsForDate($guestId, $date)
	{
		# code...
		$st = $this->_driver->prepare('select SUM(amt) as `total` from `guestPayments` where `guestId` = :guestId and `date` = :date');
		$st->bindValue(':guestId', $guestId, PDO::PARAM_INT);
		$st->bindValue(':date', $date, PDO::PARAM_STR);
		$st->execute();
		$st->bindColumn('total', $total);
		return $st->fetch(PDO::FETCH_ASSOC) ? $total : null;
	}

	public function fetchDetailsByPhone($phone)
	{
		# code...
		$query = 'select * from guestDetails where phone = :phone';
		//$st = $this->_driver->prepare('CALL sp_fetchGuestDetailsByPhone(:phone)');
		$st = $this->_driver->prepare($query);
		$st->bindValue(':phone', $phone, PDO::PARAM_STR);
		return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : false;
	}

	public function getOutstandingBal($phone)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_getGuestBal(:phone)');
		$st->bindValue(':phone', $phone, PDO::PARAM_STR);
		return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : false;
	}

	public function insertDetails(Array $params)
	{
		# guest details
		$gdInsert = $this->_driver->prepare('CALL sp_insertGuestDetails(:name, :phone, :addr, :occupation, :nationality, :reason, :noOfOccupants)');
		foreach ($params as $key => $value) {
			# code...
			if($key == 'noOfOccupants'){
				$gdInsert->bindValue(':'.$key, $value, PDO::PARAM_INT);
			}else{
				$gdInsert->bindValue(':'.$key, $value, PDO::PARAM_STR);
			}
		}
		$gdInsert->execute();
		$gdInsert->bindColumn('id', $guestId);
		$gdInsert->fetch(PDO::FETCH_ASSOC);
		$gdInsert->closeCursor();
		return $guestId;
	}

	public function getCheckInInfo($roomId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_getGuestCheckInInfo(:roomId)');
		$st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
		return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : null;
	}

	public function getCheckInInfo2($guestId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_getGuestCheckInInfo2(:guestId)');
		$st->bindValue(':guestId', $guestId, PDO::PARAM_INT);
		return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : null;
	}


	public function insertIntoRegister(Array $params)
	{
		# code...
		# guest register
		$grInsert = $this->_driver->prepare('CALL sp_insertGuestIntoRegister(:checkInDate, :checkInTime, :autoBillTime, :guestId, :roomId, :discount, :complimentary )');
		foreach ($params as $key => $value) {
			# code...
			if(in_array($key, array('guestId, discount')) !== false){
				$grInsert->bindValue(':'.$key, $value, PDO::PARAM_INT);
			}else{
				$grInsert->bindValue(':'.$key, $value, PDO::PARAM_STR);
			}
		}
		$grInsert->execute();
		$grInsert = null;

	}

	# Adds a new Bill for the guest
	public function addBill(Array $params)
	{
		global $registry;
		$thisUser = unserialize($registry->get('session')->read('thisUser'));

		$salesDetails = isset($params['salesDetails']) ? $params['salesDetails'] : json_encode(array());

		#bills
		$billsInsert = $this->_driver->prepare('CALL sp_insertGuestBill(:date, :guestId, :roomId, :transId, :amt, :billType, :details)');
		foreach ($params as $key => $value) {
			# code...
			if($key != 'staffId') {
				if ( $key != 'salesDetails' ) {
					if ( $key == 'amt' || $key == 'billType' ) {
						$billsInsert->bindValue(':' . $key, $value, PDO::PARAM_INT);
					}
					else {
						$billsInsert->bindValue(':' . $key, $value, PDO::PARAM_STR);
					}
				}
			}
		}
		$billsInsert->execute();
		$billsInsert->bindColumn('id', $billsId);
		$billsInsert->fetch(PDO::FETCH_ASSOC);
		$billsInsert->closeCursor();


		#insert bill into transactions

		return \Transaction::addNew(array(
			                        'date' => $params['date'],
			                        'time' => time() ,
			                        'transId' => $params['transId'],
			                        'transType' => 1,
			                        'src' => json_encode(array('tbl' => 'guestBills', 'id' => $billsId)),
			                        'details' => json_encode(array(
											'type' => 'Guest Bill',
											'guestId' => $params['guestId'],
											'desc' => $params['details'],
											'amt' => $params['amt'],
			                        		'salesDetails' => $salesDetails
			                        		)),

									'staffId' => isset($params['staffId']) ? $params['staffId'] : $thisUser->id,
									'privilege' => $thisUser->get('activeAcct')
			));

	}

	public function addPayment(Array $params)
	{
		//var_dump($params);die;
		global $registry;
		$thisUser = unserialize($registry->get('session')->read('thisUser'));

		#payment
		$query = '';
		$payInsert = $this->_driver->prepare('CALL sp_insertGuestPayment(:date, :guestId, :transId, :amt, :details,:roomId
		)');
		foreach ($params as $key => $value) {
			# code...
			if($key == 'guestId' || $key == 'amt' || $key == 'roomId'){
				$payInsert->bindValue(':'.$key, $value, PDO::PARAM_INT);
			}else{
				$payInsert->bindValue(':'.$key, $value, PDO::PARAM_STR);
			}
		}

//		$payInsert->bindValue(':date', $params['date'], PDO::PARAM_STR);
//		$payInsert->bindValue(':guestId', $params['guestId'], PDO::PARAM_INT);
//		$payInsert->bindValue(':transId', $params['transId'], PDO::PARAM_INT);
//		$payInsert->bindValue(':amt', $params['amt'], PDO::PARAM_INT);
//		$payInsert->bindValue(':details', $params['details'], PDO::PARAM_STR);

		$payInsert->execute();
		$payInsert->bindColumn('id', $payId);
		$payInsert->fetch(PDO::FETCH_ASSOC);
		$payInsert->closeCursor();


		# insert transactions
		return \Transaction::addNew(array(
				'date' => $params['date'],
				'time' => time() ,
				'transId' => $params['transId'],
				'transType' => 2,
				'src' => json_encode(array('tbl' => 'guestPayments', 'id' => $payId)),
				'details' => json_encode(array(
								'type' => 'Guest Payment',
								'guestId' => $params['guestId'],
								'desc' => $params['details'],
								'amt' => $params['amt'])),
			        'staffId' => $thisUser->id,
				'privilege' => $thisUser->get('activeAcct')
				));
	}


	public function addRefund(Array $data){
    	global $registry;
		$thisUser = unserialize($registry->get('session')->read('thisUser'));

		#payment
		$st = $this->_driver->prepare('CALL sp_insertGuestRefund(:date, :guestId, :transId, :amt)');
		foreach ($data as $key => $value) {
			if($key == "amt" || $key == "guestId"){
				$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
			}else{
				$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
			}
		}
		$st->execute();
		$st->bindColumn('id', $id);
		$st->fetch(PDO::FETCH_ASSOC);
		$st->closeCursor();
		$st = null;
		return \Transaction::addNew(array(
								'date' => $data['date'],
								'time' => now(),
								'transId' => $data['transId'],
								'transType' => 7,
								'src' => json_encode(array('tbl' => 'guestRefunds', 'id' => $id)),
								'details' => json_encode(array(
														'type' => 'Guest Refund',
														'guestId' => $data['guestId'],
														'desc' => 'Excess Payment Refund',
														'amt' => $data['amt'])),
								'staffId' => $thisUser->id,
								'privilege' => $thisUser->get('activeAcct')
								));
    }


	public function checkIn(Array $details, $complimentary = false, $flatRate = false)
	{

		global $registry;
		$thisUser = unserialize($registry->get('session')->read('thisUser'));

		$com = ($complimentary)  ? 'yes' : 'no';
		//var_dump($details);die;
         /*****************
		  LAST INSERT ID NOT WORKING FOR ME
		  HAD TO RETURN LAST IN FROM THE STORD PROCEDURE
         ******************/

		try{

			//$this->_driver->beginTransaction();

					#check if guest details already if
			        $chk = $this->fetchDetailsByPhone($details['phone']);

			        if($chk){
			        	$guestId = $chk->id;



			        	#update reason for visit and no_of ocuppants
			        	$query = 'update `guestDetails` set `reasonForVisit` = :reason, `noOfOccupants` = :no';
			        	if(!$complimentary){
			        		$query .= ', `discount` = :discount';
			        	}
			        	$query .= ' where `id` = :id';


			        	$st = $this->_driver->prepare($query);
			        	$st->bindValue(':reason', $details['reason'], PDO::PARAM_STR);
			        	$st->bindValue(':no', $details['noOfOccupants'], PDO::PARAM_INT);

			        	if(!$complimentary){
			        		$st->bindValue(':discount', $details['discount'], PDO::PARAM_INT);
			        	}
			        	$st->bindValue(':id', $guestId, PDO::PARAM_INT);
			        	$st->execute();
			        	$st = null;

			        }else{ # if guest details not found


						#insert into guest details
						$guestId = $this->insertDetails(array(
							'name' => ucwords($details['name']), 'phone' => $details['phone'], 'addr' => $details['addr'], 'occupation' => $details['occu'], 'nationality' => $details['nationality'], 'reason' => $details['reason'], 'noOfOccupants' => $details['noOfOccupants']
							));

				    }


					$discount = ($complimentary) ? 0 : $details['discount'];

					#insert into guest register
					$this->insertIntoRegister(array(
						'checkInDate' => $details['date'], 'checkInTime' => time(), 'autoBillTime' => autoBillTime(), 'guestId' => $guestId, 'roomId' => $details['roomNo'], 'discount' => $discount, 'complimentary' => $com
						));


					# if not complimentary check in
					if(!$complimentary){

							#insert this guest as bill payer
							\Bill::insertBillPayer(array(
								'guestId' => $guestId,
								'roomId' => $details['roomNo'],
								'billTypes' => json_encode(array('All'))
								));


							#insert into guest bills

							#generate trans id
							$transId = generateTransId();



							$billsId = $this->addBill(array(
								'date' => $details['date'], 'guestId' => $guestId, 'roomId' => $details['roomNo'], 'transId' => $transId, 'amt' => $details['bill'], 'billType' => 2, 'details' => 'Room Charge'
								));


							# if the guest made any deposit
							if($details['deposit1'] != 0){


								    switch(strtolower($details['payType'])){
								    	case 'cash' : default : case '' :
								    		$det = array('Pay Type' => 'Cash');
								    	break;

								    	case 'cheque':
								    		# code...
								    	    $det = array('Pay Type' => 'Cheque', 'Bank' => $details['chequeBank'], 'Cheque No' => $details['chequeNo']);
								    		break;

								    	case 'pos':
								    		# code...
								    		$det = array('Pay Type' => 'POS', 'POS No' => $details['posNo']);
								    		break;

								    	case 'bt':
								    		# code...
								    	    $det = array('Pay Type' => 'Bank Transfer', 'Bank' => $details['btBank'], 'Transfer Date' => $details['btDate']);
								    		break;
								    }



								    #generate trans id
									$transId = generateTransId();

									#insert guest payment
									$payId = $this->addPayment(array(
										'date' => $details['date'], 'guestId' => $guestId, 'transId' => $transId, 'amt' => $details['deposit1'], 'details' => json_encode($det), 'roomId' => $details['roomNo']
										));



						      } # end deposit Payment




						    # if the guest has an outstanding bal and the guest wants to use am
							if(isset($details['useOB']) && $details['useOB'] == 'yes'){

									#insert into guest payments

									#generate trans id
									$transId = generateTransId();

									# Add Out Bal as Payment
									$this->addPayment(array(
										'date' => $details['date'], 'guestId' => $guestId, 'transId' => $transId, 'amt' => $details['outBal'], 'details' => json_encode(array('Pay Type' => 'Outstanding Balance')), 'roomId' => $details['roomNo']
								));

									#delete this guest bal from guestbalances
									$this->deletePreviousBalance($details['phone']);
                            }

                            #if user made no deposit and has no oustanding bal
							if( $details['deposit1'] == 0 && !isset($details['useOB'])){
								# log Credit Check In
						    	$registry->get('logger')->logGuestCreditCheckIn(array(
						    								'guestId' => $guestId,
						    								'roomId' => $details['roomNo']
						    								));
			                }


			    # if compilmentary Check In
				}else{

						#insert this guest as bill payer for all bills except Room charge
						\Bill::insertBillPayer(array(
							'guestId' => $guestId,
							'roomId' => $details['roomNo'],
							'billTypes' => json_encode(array('Main Bar', 'Pool Bar', 'Reception', 'Resturant'))
							));



						# log complimentary CheckIn
						$registry->get('logger')->logComplimentaryCheckIn(array(
						    								'guestId' => $guestId,
						    								'roomId' => $details['roomNo']
						    								));
				}






			//$this->_driver->commit();

			return true;


		}catch(Exception $e){
			// $this->_driver->rollBack();
			 Error::throwException($e->getMessage());
			 return false;
		}

	}

	public function checkInFromReservation(Array $details)
	{
		# code...
		global $registry;
		$thisUser = unserialize($registry->get('session')->read('thisUser'));
         /*****************
		  LAST INSERT ID NOT WORKING FOR ME
		  HAD TO RETURN LAST IN FROM THE STORD PROCEDURE
         ******************/

		try{

			//$this->_driver->beginTransaction();

					#check if guest details already if
			        $chk = $this->fetchDetailsByPhone($details['phone']);

			        if($chk){
			        	$guestId = $chk->id;

			        	#update reason for visit and no_of ocuppants
			        	$st = $this->_driver->prepare('update `guestDetails` set `reasonForVisit` = :reason, `noOfOccupants` = :no where `id` = :id');
			        	$st->bindValue(':reason', $details['reason'], PDO::PARAM_STR);
			        	$st->bindValue(':no', $details['noOfOccupants'], PDO::PARAM_INT);
			        	$st->bindValue(':id', $guestId, PDO::PARAM_INT);
			        	$st->execute();
			        	$st = null;

			        }else{ # if guest details not found


						#insert into guest details
						$guestId = $this->insertDetails(array(
							'name' => ucwords($details['name']), 'phone' => $details['phone'], 'addr' => $details['addr'], 'occupation' => $details['occu'], 'nationality' => $details['nationality'], 'reason' => $details['reason'], 'noOfOccupants' => $details['noOfOccupants']
							));

				    }

					$discount = $details['discount'];

					#insert into guest register
					$this->insertIntoRegister(array(
						'checkInDate' => $details['date'], 'checkInTime' => time(), 'autoBillTime' => autoBillTime(), 'guestId' => $guestId, 'roomId' => $details['roomId'], 'discount' => $discount, 'complimentary' => 'no'
						));


					#insert this guest as bill payer
					\Bill::insertBillPayer(array(
						'guestId' => $guestId,
						'roomId' => $details['roomId'],
						'billTypes' => json_encode(array('All'))
						));


					#insert into guest bills

					#generate trans id
					$transId = generateTransId();

					$billsId = $this->addBill(array(
						'date' => $details['date'], 'guestId' => $guestId, 'roomId' => $details['roomId'], 'transId' => $transId, 'amt' => $details['bill'], 'billType' => 2, 'details' => 'Room Charge'
						));


					# if the guest made any deposit
					if($details['deposit1'] != 0){

						   $det = json_encode(array('Pay Type' => 'From Reservation Payment'));

						    #generate trans id
							$transId = generateTransId();

							#insert guest payment
							$payId = $this->addPayment(array(
								'date' => $details['date'], 'guestId' => $guestId, 'transId' => $transId, 'amt' => $details['deposit1'], 'details' => $det, 'roomId' => $details['roomId']
								));



				    }else{
						#if user made no deposit

						# log Credit Check In
				    	$registry->get('logger')->logGuestCreditCheckIn(array(
				    								'guestId' => $guestId,
				    								'roomId' => $details['roomId']
				    								));
	                }



			return true;


		}catch(Exception $e){
			// $this->_driver->rollBack();
			 Error::throwException($e->getMessage());
			 return false;
		}
	}

	public function deletePreviousBalance($phone)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_deleteGuestBalance(:phoneNo)');
		$st->bindValue(':phoneNo', $phone, PDO::PARAM_INT);
		$st->execute();
		$st = null;
	}

	public function changeIdInBillsTbl(Array $data)
	{


		$st = $this->_driver->prepare('CALL sp_changeGuestBillsGuestId(:id, :newGuestId)');

		# code...
		foreach (\Bill::fetchByRoomId($data['roomId']) as $key) {

			# if date is latter than or equal to the start date
			if(strtotime($key->date) >= strtotime($data['startDate'])){

				# check billTypes to cover

				# if billtypes is All
				if($data['billTypes'] == 'All'){

					# update bill guest id
					$st->bindValue(':id', $key->id, PDO::PARAM_INT);
					$st->bindValue(':newGuestId', $data['newGuestId'], PDO::PARAM_INT);
					$st->execute();


			    # if bill Type is room charge
			    }elseif($data['billTypes'] == 'roomCharge' && $key->billType == 2){
			    	//echo $key['id']; die;
					# update bill guest id
					$st->bindValue(':id', $key->id, PDO::PARAM_INT);
					$st->bindValue(':newGuestId', $data['newGuestId'], PDO::PARAM_INT);
					$st->execute();


			    }elseif($data['billTypes'] == 'POSUnits' && $key->billType > 2){
			    	//echo $key['id']; die;
		    		# update bill guest id
					$st->bindValue(':id', $key->id, PDO::PARAM_INT);
					$st->bindValue(':newGuestId', $data['newGuestId'], PDO::PARAM_INT);
					$st->execute();
			    }
			}

		}

	}


	public function addOutstandingBal(Array $data)
    {
    	$st = $this->_driver->prepare('CALL sp_insertGuestOutstandingBal(:phone, :amt)');
		foreach ($data as $key => $value) {
			if($key == "amt"){
				$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
			}else{
				$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
			}
		}
        return $st->execute() ? true : false;
    }

    public function addCredit(Array $data)
    {
    	global $registry;
		$thisUser = unserialize($registry->get('session')->read('thisUser'));

		# if the function is bieng called from guestModell::submitCheckout
		# override params
		if(isset($data[1])){
           $guest = new \Guest($data[0]);
			$checkInDetails = $guest->getCheckInInfo2();
           $amt = $data[1];
           $data = array(
           				'date' => today(),
           				'guestId' => $guest->id,
           				'guestPhone' => $guest->phone,
           				'transId' => generateTransId(),
           				'amt' => $amt,
           				'details' => 'From Credit Check out',
				   		'roomId' => $checkInDetails->roomId
           				);
		}

    	$st = $this->_driver->prepare('CALL sp_insertGuestCredit(:date, :guestId, :guestPhone, :transId, :amt, :details, :roomId)');
		foreach ($data as $key => $value) {
			if($key == "amt" || $key == "guestId"){
				$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
			}else{
				$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
			}
		}
		$st->execute();
		$st->bindColumn('id', $id);
		$st->fetch(PDO::FETCH_ASSOC);
		$st->closeCursor();
		$st = null;
        return \Transaction::addNew(array(
								'date' => $data['date'],
								'time' => now(),
								'transId' => $data['transId'],
								'transType' => 8,
								'src' => json_encode(array('tbl' => 'guestCredits', 'id' => $id)),
								'details' => json_encode(array(
														'type' => 'Guest Credit',
														'guestId' => $data['guestId'],
														'desc' => $data['details'] . 'of ' . $guest->name,
														'amt' => $data['amt'])),
								'staffId' => $thisUser->id,
								'privilege' => $thisUser->get('activeAcct')
								));

    }

    public function addCreditPayment(Array $data)
    {
    	# code...
    	global $registry;
		$thisUser = unserialize($registry->get('session')->read('thisUser'));

    	$st = $this->_driver->prepare('CALL sp_insertGuestCreditPayment(:date, :guestId, :guestPhone, :transId, :amt, :details)');
		foreach ($data as $key => $value) {
			if($key == "amt" || $key == "guestId"){
				$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
			}else{
				$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
			}
		}
        $st->execute();
		$st->bindColumn('id', $id);
		$st->fetch(PDO::FETCH_ASSOC);
		$st->closeCursor();
		$st = null;
        return \Transaction::addNew(array(
								'date' => $data['date'],
								'time' => now(),
								'transId' => $data['transId'],
								'transType' => 9,
								'src' => json_encode(array('tbl' => 'guestCreditPayments', 'id' => $id)),
								'details' => json_encode(array(
															'type' => 'Guest Credit Payment',
															'guestId' => $data['guestId'],
															'desc' => $data['details'],
															'amt' => $data['amt']
															)),
								'staffId' => $thisUser->id,
								'privilege' => $thisUser->get('activeAcct')
								));

    }

    public function addStayActivity(Array $data)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_insertGuestStayInfo(:guestPhone, :stayInfo, :bills, :payments)');
		foreach ($data as $key => $value) {
			$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
		}
        return $st->execute() ? true : false;
    }

    public function deleteFromRegister($guestId, $roomId)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_deleteGuestFromRegister(:guestId, :roomId)');
		$st->bindValue(':guestId', $guestId, PDO::PARAM_INT);
		$st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
        return $st->execute() ? true : false;
    }

    public function addAsBillPayer(Array $values)
    {
    	# code...
		$st = $this->_driver->prepare('CALL sp_InsertBillPayer(:guestId, :roomId, :billTypes)');
		$st->bindValue(':guestId', $values['guestId'], PDO::PARAM_INT);
		$st->bindValue(':roomId', $values['roomId'], PDO::PARAM_INT);
		$st->bindValue(':billTypes', $values['billTypes'], PDO::PARAM_STR);
		return $st->execute() ? true : false;
    }

    public function deleteFromBillPayers($guestId, $roomId)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_deleteGuestFromBillPayers(:guestId, :roomId)');
		$st->bindValue(':guestId', $guestId, PDO::PARAM_INT);
		$st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
        return $st->execute() ? true : false;
    }

    public function updateRoomInRegister($params)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_updateGuestRoomInRegister(:guestId, :oldRoomId, :newRoomId)');
    	foreach ($params as $key => $value) {
    		# code...
    		$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
    	}
    	return $st->execute() ? true : false;

    }

    public function updateBills(Array $params)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_updateBillsForGuest(:date, :guestId, :amt, :billType)');
    	foreach ($params as $key => $value) {
    		# code...
    		if($key == 'date'){
    			$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
    		}else{
    			$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
    		}
    	}
    	//$st->bindValue(':billType', 2);
    	$st->execute();
    	$st->bindColumn('tId', $transId);
    	$st->fetch(PDO::FETCH_ASSOC);
    	return $transId;

    }

    public function updateDiscount(Array $params)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_updateGuestDiscount(:guestId, :roomId, :discount)');
    	foreach ($params as $key => $value) {
    		# code...
    		$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
    	}
    	return $st->execute() ? true : false;
    }

    public function fetchPreviousGuestDetails($guestPhone)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_fetchPreviousGuestDetails(:phone)');
    	$st->bindValue(':phone', $guestPhone, PDO::PARAM_STR);
    	return $st->execute() ? $st->fetchAll(PDO::FETCH_ASSOC) : array();
    }

    public function fetchNameByPhone($guestPhone)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_fetchGuestNameByPhone(:guestPhone)');
    	$st->bindValue(':guestPhone', $guestPhone, PDO::PARAM_STR);
    	$st->execute();
    	$st->bindColumn('name', $name);
    	$st->fetch(PDO::FETCH_ASSOC);
    	$st = null;
    	return $name;
    }

    public function fetchPreviousGuestCredits($guestPhone)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_fetchPreviousGuestCredits(:guestPhone)');
    	$st->bindValue(':guestPhone', $guestPhone, PDO::PARAM_STR);
    	return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array() ;
    }

    public function fetchPreviousGuestPayments($guestPhone)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_fetchPreviousGuestCreditPayments(:guestPhone)');
    	$st->bindValue(':guestPhone', $guestPhone, PDO::PARAM_STR);
    	return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array() ;
    }


    public function fetchTotalPreviousGuestCredits($guestPhone)
    {
    	# code...
    	$st = $this->_driver->prepare('select SUM(amt) as `total` from guestCredits where guestPhone = :guestPhone');
    	$st->bindValue(':guestPhone', $guestPhone, PDO::PARAM_STR);
    	$st->execute();
    	$st->bindColumn('total', $total);
    	$st->fetch();
    	return $total;
    }

    public function fetchTotalPreviousGuestPayments($guestPhone)
    {
    	# code...
    	$st = $this->_driver->prepare('select SUM(amt) as `total` from guestCreditPayments where guestPhone = :guestPhone');
    	$st->bindValue(':guestPhone', $guestPhone, PDO::PARAM_STR);
    	$st->execute();
    	$st->bindColumn('total', $total);
    	$st->fetch();
    	return $total;
    }




    public function updateAutoBillTime(Array $data)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_updateGuestAutoBillTime(:id, :autoBillTime)');
    	$st->bindValue(':id', $data['id'], PDO::PARAM_INT);
    	$st->bindValue(':autoBillTime', $data['autoBillTime'], PDO::PARAM_STR);
    	return $st->execute() ? true : false;
    }

    public function fetchDistinctDebtors()
    {
    	# code...
    	$st = $this->_driver->prepare('select distinct `guestId` from `guestCredits`');
    	return $st->execute() ? $st->fetchAll() : array();
    }

	public function storeGuestMapping(Array $data){
		$st = $this->_driver->prepare('insert into guestMappings ( date, mappings ) values ( :date, :mappings )');
		$st->bindValue(':date', $data['date'], PDO::PARAM_STR);
		$st->bindValue(':mappings', $data['mappings'], PDO::PARAM_STR);
		return $st->execute() ? true : false;
	}


	public function autoBillAlreadyExecuted($date){
		$st = $this->_driver->prepare('select id from guestMappings where date = :date');
		$st->bindValue(':date', $date, PDO::PARAM_STR);
		$st->execute();
		$st->bindColumn('id', $id);
		$st->fetch();
		return (is_null($id) || false === $id) ? false : true;
	}

	public function fetchGuestBalances(){
		$st = $this->_driver->prepare('select * from guestBalances');
		return $st->execute() ? $st->fetchAll() : array();
	}








#end of class
}
