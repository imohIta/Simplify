<?php
namespace application\libs;
use \PDO;
use core\libs\Database as Db;
/**
*
*
*/
defined('ACCESS') || AppError::exitApp();

class Database extends Db{

    public function addNewUserAcct(Array $data)
    {
        #
        $st = $this->_driver->prepare('insert into `users` ( staffId, username, pwd ) values ( :staffId, :username, :pwd )');
        foreach ($data as $key => $value) {
            if($key == 'staff'){
                $st->bindValue(':'.$key, $value, PDO::PARAM_INT);
            }else{
                $st->bindValue(':'.$key, $value, PDO::PARAM_STR);
            }
        }
        return $st->execute() ? true : false;
    }

	public function fetchUserDetails($userId)
	{
		#
		$st = $this->_driver->prepare('CALL sp_fetchUserDetails(:userId)');
		$st->bindValue(':userId', $userId, PDO::PARAM_INT);
		return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : null;
	}

    public function fetchUserDetails2($staffId)
    {
        #
        $st = $this->_driver->prepare('select * from users where staffId = :staffId');
        $st->bindValue(':staffId', $staffId, PDO::PARAM_INT);
        return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : null;
    }

    public function fetchAllUsers($includeMgtStaff)
    {
        # code...
        $query = 'select * from `staff` where `deptId` != 1';
        if(!$includeMgtStaff){
            $query .= ' and `deptId` not in (2,3,4,5,6)';
        }
        $st = $this->_driver->prepare($query);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }


	public function getUserRole($priv)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_getUserRole(:priv)');
		$st->bindValue(':priv', $priv, PDO::PARAM_INT);
		$st->execute();
		$st->bindColumn('role', $role);
		$st->fetch(PDO::FETCH_ASSOC);
		$st = null;
		return $role;
	}

	public function getPwdHash($username)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_getPwdHash(:username)');
		$st->bindValue(':username', $username, PDO::PARAM_STR);
		$st->execute();
		$st->bindColumn('hash', $hash);
		$st->bindColumn('id', $id);
        $st->bindColumn('staffId', $staffId);
		$st->fetch(PDO::FETCH_ASSOC);
		$st = null;
		return array('hash' => $hash, 'id' => $id, 'staffId' => $staffId);
	}

	public function updatePwdHash($id, $newHash)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_updatePwdHash(:id, :newHash)');
		$st->bindValue(':id', $id, PDO::PARAM_INT);
		$st->bindValue(':newHash', $newHash, PDO::PARAM_STR);
		return $st->execute() ? true : false;
	}

    public function deleteUser($staffId)
    {
        # code...

        $st = $this->_driver->prepare('delete from `users` where `staffId` = :staffId');
        $st->bindValue(':staffId', $staffId, PDO::PARAM_INT);
        $st->execute();

        $st2 = $this->_driver->prepare('delete from staff where id = :id');
        $st2->bindValue(':id', $staffId, PDO::PARAM_INT);
        return $st2->execute() ? true : array();
    }




    public function addReservation(Array $data)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_addReservation(:date, :startDate, :endDate, :guestName, :guestPhone, :roomId, :reserveId)');
    	foreach ($data as $key => $value) {
    		if($key == 'roomId'){
    			$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
    		}else{
    			$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
    		}
    	}
    	return $st->execute() ? true : false;
    }

    public function updateReservation(Array $data)
    {
    	# code...
    	if($data['src'] == 'app'){
    		$st = $this->_driver->prepare('CALL sp_editAppReservation(:startDate, :endDate, :guestName, :guestPhone, :roomId, :reserveId)');
    	}else{
    		$st = $this->_driver->prepare('CALL sp_editWebReservation(:startDate, :endDate, :guestName, :guestPhone, :roomId, :reserveId)');
    	}
    	foreach ($data as $key => $value) {
    		if($key != 'src'){
	    		if($key == 'roomId'){
	    			$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
	    		}else{
	    			$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
	    		}
    	    }
    	}
    	return $st->execute() ? true : false;
    }

    public function deleteReservation(Array $data)
    {
    	# code...
    	if($data['src'] == 'app'){
    		$st = $this->_driver->prepare('CALL sp_deleteAppReservation(:roomId, :reserveId)');
    	}else{
    		$st = $this->_driver->prepare('CALL sp_deleteWebReservation(:roomId, :reserveId)');
    	}
    	$st->bindValue(':roomId', $data['roomId'], PDO::PARAM_INT);
    	$st->bindValue(':reserveId', $data['reverseId'], PDO::PARAM_STR);
    	return $st->execute() ? true : false;
    }

    public function deleteReservationFull($revId)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_deleteReservationFull(:reserveId)');
    	$st->bindValue(':reserveId', $revId, PDO::PARAM_STR);
    	return $st->execute() ? true : false;
    }

    public function deleteReservationPayments($revId)
    {
        # code...
        $st = $this->_driver->prepare('CALL sp_deleteReservationPayments(:reserveId)');
        $st->bindValue(':reserveId', $revId, PDO::PARAM_STR);
        return $st->execute() ? true : false;
    }

    public function addReservationPayment(Array $data)
    {
    	$st = $this->_driver->prepare('CALL sp_addReservationPayment(:date, :reserveId, :amt, :details, :src)');
    	foreach ($data as $key => $value) {
    		if($key == 'amt'){
    			$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
    		}else{
    			$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
    		}
    	}
    	$st->execute();
    	$st->bindColumn('id', $id);
    	$st->fetch(PDO::FETCH_ASSOC);
    	return $id;
    }

    public function fetchReservations($src)
    {
    	# code...
    	if($src == 'app'){
    		$st = $this->_driver->prepare('CALL sp_fetchAppReservations()');
    	}elseif($src == 'web'){
    		$st = $this->_driver->prepare('CALL sp_fetchWebReservations()');
    	}
    	return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function fetchReservedRoomsByResId($resId, $src)
    {
    	if($src == 'app'){
    		$st = $this->_driver->prepare('CALL sp_fetchAppReservationsByResId(:resId)');
    	}elseif($src == 'web'){
    		$st = $this->_driver->prepare('CALL sp_fetchWebReservationsByResId(:resId)');
    	}
    	$st->bindValue(':resId', $resId, PDO::PARAM_STR);
    	return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function fetchReservationPayments($resId)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_fetchReservationsPaymentsByResId(:resId)');
    	$st->bindValue(':resId', $resId, PDO::PARAM_STR);
    	return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();

    }

    public function addChairmanXpenses(Array $data)
    {
        # code...
        global $registry;
        $thisUser = unserialize($registry->get('session')->read('thisUser'));

        $st = $this->_driver->prepare('CALL sp_addChairmanXpenses(:date, :details, :transId, :amt, :staffId)');
        foreach ($data as $key => $value) {
            if($key == 'amt'){
                $st->bindValue(':'.$key, $value, PDO::PARAM_INT);
            }else{
                $st->bindValue(':'.$key, $value, PDO::PARAM_STR);
            }
        }
        $st->bindValue(':staffId', $thisUser->id, PDO::PARAM_INT);
        $st->execute();
        $st->bindColumn('id', $id);
        $st->fetch(PDO::FETCH_ASSOC);
        $st->closeCursor();

        return \Transaction::addNew(array(
                                    'date' => $data['date'],
                                    'time' => time() ,
                                    'transId' => $data['transId'],
                                    'transType' => 11,
                                    'src' => json_encode(array('tbl' => 'chairmanXpenses', 'id' => $id)),
                                    'details' => json_encode(array(
                                            'type' => 'Chairman Expenses',
                                            'desc' => $data['details'],
                                            'amt' => $data['amt'])),
                                    'staffId' => $thisUser->id,
                                    'privilege' => $thisUser->get('activeAcct')
            ));

    }

    public function fetchUserNotifications($userPrivilege, $limit)
    {
        global $registry;
        $thisUser = unserialize($registry->get('session')->read('thisUser'));


        # some acct do not have notification
        # kitchen, store, purchaser, cashier
        if(in_array($userPrivilege, array(6,12,15)) !== false){

            return array();

        }else{
            $query = '';
            switch ($userPrivilege) {
                # Admin, Manager, Auditor .... See All except Guest Autobilling
                case 1: case 2: case 3: case 4: case 5:
                    $query = 'select * from `notifications` where `notType` != 14 order by `id` desc';
                    break;

                # POS Units
                # see transaction reversal, requisition approval
                case 8: case 9: case 10: case 11:

                    $query = 'select * from `notifications` where `notType` in ( 11, 13 ) and `targetStaffId` = ' . $thisUser->id . ' order by `id` desc';
                    break;

                # Reception
                case 7:
                    $query = 'select * from `notifications` where `notType` = 11  and `targetStaffId` = ' . $thisUser->id . ' or `notType` = 14 order by `id` desc';
                    break;

                case 13 : #Store
                      $query = 'select * from `notifications` where `notType` = 18 order by `id` desc';
                    break;

                case 14: #Purchaser
                    $query = 'select * from `notifications` where `notType` in (18,19) order by `id` desc';
                    break;
            }
            if($limit){
                $query .= ' limit :limit';
            }
            $st = $this->_driver->prepare($query);
            if($limit){ $st->bindValue(':limit', $limit, PDO::PARAM_INT); }
            return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();

       }


    }

    public function countUnreadNotifications($userId)
    {

    }


    public function fetchStockItems($tbl)
    {
        # code...
        $st = $this->_driver->prepare('select * from ' . $tbl);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function addUnpostedSale(Array $data, $transId, $guestType, $roomId, $type)
    {

        # itemId, itemName, price ,qty, amt
        global $registry;
        $thisUser = unserialize($registry->get('session')->read('thisUser'));

        # if user active acct is reception ... object is 1 for menu ... else 2 for Item
        $object = $thisUser->get('activeAcct') == 10 ? 1 : 2;

        # since both food and drinks are now sold from resturant account
        # dynamically insert drinks as bieng sold by resturant drinks

        if($object == 1){
            $priv = $type == "item" ? 11 : 10;
        }else{
            $priv = $thisUser->get('activeAcct');
        }

        $st = $this->_driver->prepare('CALL sp_addUnpostedSale(:date, :transId, :object, :objectId, :qty, :price, :guestType, :roomId, :staffId, :privilege, :time)');
        $st->bindValue(':date', today());
        $st->bindValue(':time', time());
        $st->bindValue(':transId', $transId);
        $st->bindValue(':object', $object);
        $st->bindValue(':objectId', $data['itemId'], PDO::PARAM_INT);
        $st->bindValue(':qty', $data['qty'], PDO::PARAM_INT);
        $st->bindValue(':price', $data['price'], PDO::PARAM_INT);
        $st->bindValue(':guestType', $guestType, PDO::PARAM_INT);
        $st->bindValue(':roomId', $roomId, PDO::PARAM_INT);
        $st->bindValue(':staffId', $thisUser->staffId, PDO::PARAM_INT);
        $st->bindValue(':privilege', $priv, PDO::PARAM_INT);

        return $st->execute() ? true : false;



    }

    public function addClosingStockSnapShot(Array $data)
    {
        # code...
        $st = $this->_driver->prepare('CALL sp_addClosingStockSnapShot(:date, :time, :staffId, :privilege, :stock)');
        foreach ($data as $key => $value) {
            # code...
            if($key == 'staffId' || $key == 'privilege'){
                $st->bindValue(':'.$key, $value, PDO::PARAM_INT);
            }else{
               $st->bindValue(':'.$key, $value, PDO::PARAM_STR);
            }
        }
        return $st->execute() ? true : false;
    }

    public function updateClosingStockSnapShot(Array $data)
    {
        # code...
        $st = $this->_driver->prepare('CALL sp_updateClosingStockSnapShot(:date, :time, :staffId, :privilege, :stock)');
        foreach ($data as $key => $value) {
            # code...
            if($key == 'staffId' || $key == 'privilege'){
                $st->bindValue(':'.$key, $value, PDO::PARAM_INT);
            }else{
               $st->bindValue(':'.$key, $value, PDO::PARAM_STR);
            }
        }
        return $st->execute() ? true : false;
    }

    public function closingStockAdded($date, $privilege)
    {
        # code...
        $st = $this->_driver->prepare('CALL sp_checkClosingStockAdded(:date, :priv)');
        $st->bindValue(':date', $date, PDO::PARAM_STR);
        $st->bindValue(':priv', $privilege, PDO::PARAM_INT);
        $st->execute();
        $st->bindColumn('id', $id);
        $st->fetch(PDO::FETCH_ASSOC);
        return (is_null($id) || $id === false) ? false : true;
    }

    public function fetchUnpostedSales()
    {
        # code...
        global $registry;
        $thisUser = unserialize($registry->get('session')->read('thisUser'));

        //$priv = $thisUser->get('activeAcct') == 10 ? 11 : $thisUser->get('activeAcct');

        $priv = $thisUser->get('activeAcct');

        $st = $this->_driver->prepare('CALL sp_fetchUnpostedSales(:privilege)');
        $st->bindValue(':privilege', $priv, PDO::PARAM_INT);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function fetchUnpostedSaleByTransId($transId)
    {
        # code...
        $st = $this->_driver->prepare('CALL sp_fetchUnpostedSaleByTransId(:transId)');
        $st->bindValue(':transId', $transId, PDO::PARAM_STR);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function updateUnpostedSale($transId)
    {
        # code...
        $st = $this->_driver->prepare('CALL sp_updateUnpostedSales(:transId)');
        $st->bindValue(':transId', $transId, PDO::PARAM_STR);
        return $st->execute() ?  true : false;
    }

    public function addDebtor(Array $data)
    {
        # code...

        //$st = $this->_driver->prepare('CALL sp_addCreditor(:date, :debtorName, :debtorType, :transId, :details, :staffId, :privilege, :amt)');
        $st = $this->_driver->prepare('insert into `credits` ( date, debtorName, debtorType, transId, details, amt, staffId, privilege ) values ( :date, :debtorName, :debtorType, :transId, :details, :amt, :staffId, :privilege )');
        foreach ($data as $key => $value) {
            //if($key != 'debtorType'){
                # code...
                if($key == 'staffId' || $key == 'privilege' || $key == 'amt'){
                    $st->bindValue(':'.$key, $value, PDO::PARAM_INT);
                }else{
                    $st->bindValue(':'.$key, $value, PDO::PARAM_STR);
                }
            //}
        }
        return $st->execute() ? true : false;
    }



    public function fetchAllCredits()
    {
        # code...
        global $registry;
        $thisUser = unserialize($registry->get('session')->read('thisUser'));
        $st = $this->_driver->prepare('CALL sp_fetchAllCredits(:staffId, :privilege)');
        $st->bindValue(':staffId', $thisUser->id, PDO::PARAM_INT);
        $st->bindValue(':privilege', $thisUser->get('activeAcct'), PDO::PARAM_INT);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function addCreditPayment(Array $data)
    {
        # code...
        global $registry;
        $thisUser = unserialize($registry->get('session')->read('thisUser'));


        //$query = 'insert into `creditPayments` ( date, transId, amt, creditTransId, details, staffId, privilege ) values ( :date, :transId, :amt, :creditTransId, :details, :staffId, :privilege) ';
        //$st = $this->_driver->prepare($query);


        $st = $this->_driver->prepare('CALL sp_addCreditPayment(:date, :transId, :amt, :creditTransId, :details, :staffId, :privilege)');
        foreach ($data as $key => $value) {
            # code...
            if($key == 'amt'){
                $st->bindValue(':'.$key, $value, PDO::PARAM_INT);
            }else{
                $st->bindValue(':'.$key, $value, PDO::PARAM_STR);
            }
        }
        $st->bindValue(':staffId', $thisUser->id, PDO::PARAM_INT);
        $st->bindValue(':privilege', $thisUser->get('activeAcct'), PDO::PARAM_INT);

        $st->execute();
        $st->bindColumn('id', $id);
        $st->fetch(PDO::FETCH_ASSOC);
        $st->closeCursor();

        $type = json_decode($data['details'], true);
        $type = $type['Pay Type'];

        return \Transaction::addNew(array(
            'date' => $data['date'],
            'time' => now(),
            'transId' => $data['transId'],
            'transType' => 12,
            'src' => json_encode(array('tbl' => 'credits', 'id' => $id)),
            'details' => json_encode(array(
                                    'type' => $type,
                                    'amt' => $data['amt'])),
            'staffId' => $thisUser->id,
            'privilege' => $thisUser->get('activeAcct')

            )) ? true : false;
    }


    public function fetchAllCreditPayments()
    {
        # code...
        global $registry;
        $thisUser = unserialize($registry->get('session')->read('thisUser'));

        $st = $this->_driver->prepare('CALL sp_fetchAllCreditPayments(:staffId, :privilege)');
        $st->bindValue(':staffId', $thisUser->id, PDO::PARAM_INT);
        $st->bindValue(':privilege', $thisUser->get('activeAcct'), PDO::PARAM_INT);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function fetchCreditByTransId($transId)
    {
        # code...
        $st = $this->_driver->prepare('CALL sp_fetchCreditByTransId(:transId)');
        $st->bindValue(':transId', $transId, PDO::PARAM_STR);
        return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : array();
    }

    public function removeStockItem(Array $data)
    {
        # code...
        $query = 'update ' . $data['tbl'] . ' set `qtyInStock` = `qtyInStock` - :qty where `itemId` = :itemId';
        $st = $this->_driver->prepare($query);
        $st->bindValue(':qty', $data['qty'], PDO::PARAM_INT);
        $st->bindValue(':itemId', $data['itemId'], PDO::PARAM_INT);
        if($st->execute()){
            $t = $this->_driver->prepare('insert into `stockItemRemovals` ( date, dept, itemId, qty, reason, staffId ) values ( :date, :dept, :itemId, :qty, :reason, :staffId )');
            $t->bindValue(':date', today(), PDO::PARAM_STR);
            $t->bindValue(':dept', $data['privilege'], PDO::PARAM_INT);
            $t->bindValue(':itemId', $data['itemId'], PDO::PARAM_INT);
            $t->bindValue(':qty', $data['qty'], PDO::PARAM_INT);
            $t->bindValue(':reason', $data['reason'], PDO::PARAM_STR);
            $t->bindValue(':staffId', $data['staffId'], PDO::PARAM_INT);

            return $t->execute() ? true : false;
        }
        return false;
    }

    public function addRequisition(Array $data)
    {
        # code...
        $st = $this->_driver->prepare('CALL sp_addRequisition(:date, :time, :itemId, :qty, :staffId, :privilege)');
        foreach ($data as $key => $value) {
            # code...
            if($key == 'date' || $key == 'time'){
                $st->bindValue(':'.$key, $value, PDO::PARAM_STR);
            }else{
                $st->bindValue(':'.$key, $value, PDO::PARAM_INT);
            }
        }
        return $st->execute() ? true : false;
    }

    public function fetchIssuedRequisitions($beginDate = '', $endDate = '')
    {
        # code...
        $query = "select * from `requisitions` where `issued` = 1";
        if($beginDate){
            $query .= " and `date` between '" . $beginDate . "' and '" . $endDate . "'";
           // echo $query; die;
        }


        $st = $this->_driver->prepare($query);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();

    }

    public function fetchUnIssuedRequisitions()
    {
        # code...
        $st = $this->_driver->prepare('CALL sp_fetchUnIssuedRequisitions()');
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function updateRequisition($id)
    {
        # code...
        $st = $this->_driver->prepare('CALL sp_updateRequisition(:id)');
        $st->bindValue(':id', $id, PDO::PARAM_INT);
        return $st->execute() ? true : false;
    }

    public function deleteRequisition($id)
    {
        # code...
        $st = $this->_driver->prepare('delete from requisitions where id = :id');
        $st->bindValue(':id', $id, PDO::PARAM_INT);
        return $st->execute() ? true : false;
    }


    /***********************************
    Convert all Raw Sql here to stored procedures Later...Writting raw sql
    to quicken the job
    ************************************/

    public function addStaffCredit(Array $data)
    {
        # code...
        $st = $this->_driver->prepare('insert into `staffCredits` ( date, transId, staffId, details, seller, dept, amt ) values ( :date, :transId, :staffId, :details, :seller, :dept, :amt )');
        foreach ($data as $key => $value) {
            # code...
            if(in_array($key, array('staffId','seller', 'dept')) !== false){
                $st->bindValue(':'.$key, $value, PDO::PARAM_INT);
            }else{
                $st->bindValue(':'.$key, $value, PDO::PARAM_STR);
            }
        }
        return $st->execute() ? true : false;
    }


    public function fetchSalesByTransId($transId)
    {
        # code...
        $st = $this->_driver->prepare('select * from `sales` where `transId` = :transId');
        $st->bindValue(':transId', $transId, PDO::PARAM_STR);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function addStockPurchase(Array $data)
    {
        # code...
        $st = $this->_driver->prepare('insert into `stockPurchases` ( date, purchase, staffId ) values ( :date, :purchases, :staffId)');
        foreach ($data as $key => $value) {
            # code...
            if($key == 'staffId'){
                $st->bindValue(':'.$key, $value, PDO::PARAM_INT);
            }else{
                $st->bindValue(':'.$key, $value, PDO::PARAM_STR);
            }
        }
        return $st->execute() ? true : false;
    }

    public function addBankDeposit(Array $data)
    {
        # code...
        global $registry;
        $thisUser = unserialize($registry->get('session')->read('thisUser'));

        $st = $this->_driver->prepare('insert into `bankDeposits` ( date, payDate, transId, bank, amt, staffId ) values ( :date, :payDate,  :transId, :bank, :amt, :staffId )');
        foreach ($data as $key => $value) {
            # code...
            if($key == 'amt'){
                 $st->bindValue(':'.$key, $value, PDO::PARAM_INT);
            }else{
                $st->bindValue(':'.$key, $value, PDO::PARAM_STR);
            }
            $st->bindValue(':staffId', $thisUser->id, PDO::PARAM_INT);
        }
        $st->execute();
        $id = $this->_driver->lastInsertId();

        return \Transaction::addNew(array(
            'date' => today(),
            'time' => now(),
            'transId' => $data['transId'],
            'transType' => 13,
            'src' => json_encode(array('tbl' => 'bankDeposits', 'id' => $id)),
            'details' => json_encode(array(
                                    'type' => 'Bank Deposit',
                                    'amt' => $data['amt'])),
            'staffId' => $thisUser->staffId,
            'privilege' => $thisUser->get('activeAcct')

            )) ? true : false;
    }

    public function fetchBankDeposits($date = '')
    {
        # code...
        $query = 'select * from `bankDeposits`';
        if($date){
            $query .= ' where `date` = :date';
        }
        $st = $this->_driver->prepare($query);
        if($date){ $st->bindValue(':date', $date, PDO::PARAM_STR); }
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function fetchBankDepositsForDateRange($month, $year)
    {
        # code...
        $y = filter_var($year, FILTER_SANITIZE_NUMBER_INT);
        $m = filter_var($month, FILTER_SANITIZE_NUMBER_INT);

        $beginDate = $y . '-' . $m . '-01';
        $endDate = $y . '-' . $m . '-' . getMonthLastDate($m);

        $query = 'select * from `bankDeposits` where `date` between :beginDate and :endDate';

        $st = $this->_driver->prepare($query);
        $st->bindValue(':beginDate', $beginDate, PDO::PARAM_STR);
        $st->bindValue(':endDate', $endDate, PDO::PARAM_STR);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function fetchClosingStock(Array $data)
    {
        # code...
        $st = $this->_driver->prepare('select * from `closingStock` where `date` = :date and `privilege` = :privilege');
        $st->bindValue(':date', $data['date'], PDO::PARAM_STR);
        $st->bindValue(':privilege', $data['privilege'], PDO::PARAM_INT);
        return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : array();
    }

    public function fetchOpeningStock(Array $data)
    {
        # code...
        $st = $this->_driver->prepare('select * from `closingStock` where `privilege` = :privilege and `staffId` != :staffId order by `id` desc limit 1');
        $st->bindValue(':staffId', $data['staffId'], PDO::PARAM_INT);
        $st->bindValue(':privilege', $data['privilege'], PDO::PARAM_INT);
        return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : array();
    }




    public function fetchAllPrivileges($includeMgtStaff)
    {
        # code...
        $query = 'select * from `userPrivileges` where `id` != 1';
        if(!$includeMgtStaff){ # exclude manager & auditor
            $query .= ' and `id` not in (2,3)';
        }
        $st = $this->_driver->prepare($query);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function addCashierCollection(Array $data)
    {
        # code...
        global $registry;
        $thisUser = unserialize($registry->get('session')->read('thisUser'));

        $st = $this->_driver->prepare('insert into `cashierCollections` ( date, returnDate, transId, refNo, amtPayable, amtPaid, staffId, privilege ) values ( :date, :returnDate, :transId, :refNo, :amtPayable, :amtPaid, :staffId, :privilege )');
        foreach ($data as $key => $value) {
            # code...
            if(in_array($key, array('amtPayable','amtPaid', 'staffId', 'privilege')) !== false){
                $st->bindValue(':'.$key, $value, PDO::PARAM_INT);
            }else{
               $st->bindValue(':'.$key, $value, PDO::PARAM_STR);
            }
        }
        $st->execute();
        $id = $this->_driver->lastInsertId();

        return \Transaction::addNew(array(
                'date' => today(),
                'time' => now(),
                'transId' => $data['transId'],
                'transType' => 14,
                'src' => json_encode(array('tbl' => 'cashierCollections', 'id' => $id)),
                'details' => json_encode(array(
                                        'type' => 'Cashier Collection',
                                        'amt' => $data['amtPaid'])),
                'staffId' => $thisUser->id,
                'privilege' => $thisUser->get('activeAcct')
                )) ? true : false;
    }

    public function addDeptCredit(Array $data)
    {
        # code...
        global $registry;
        $thisUser = unserialize($registry->get('session')->read('thisUser'));


        $st = $this->_driver->prepare('insert into `deptCredits` ( date, transId, amt, staffId, privilege ) values ( :date, :transId, :amt, :staffId, :privilege )');
        foreach ($data as $key => $value) {
            # code...
            if(in_array($key, array('amt', 'staffId', 'privilege')) !== false){
                $st->bindValue(':'.$key, $value, PDO::PARAM_INT);
            }else{
               $st->bindValue(':'.$key, $value, PDO::PARAM_STR);
            }
        }
        $st->execute();
        $id = $this->_driver->lastInsertId();

        return \Transaction::addNew(array(
                'date' => today(),
                'time' => now(),
                'transId' => $data['transId'],
                'transType' => 15,
                'src' => json_encode(array('tbl' => 'deptCredits', 'id' => $id)),
                'details' => json_encode(array(
                                        'type' => 'Dept Credit',
                                        'amt' => $data['amt'])),
                'staffId' => $thisUser->id,
                'privilege' => $thisUser->get('activeAcct')
                )) ? true : false;
    }

    public function fetchAllDeptCredits()
    {
        # code...
        $st = $this->_driver->prepare('select * from `deptCredits`');
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function fetchDeptCredit($date, $priv)
    {
        # code...
        $st = $this->_driver->prepare('select * from `deptCredits` where `date` = :date and `privilege` = :priv');
        $st->bindValue(':date', $date, PDO::PARAM_STR);
        $st->bindValue(':priv', $priv, PDO::PARAM_INT);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function fetchDeptCreditPaymentsDetails($transId = '', $date, $priv)
    {
        # code...
        $query = 'select * from `deptCreditPayments` where `creditDate` = :date and `privilege` = :priv';
        if($transId){
            $query .= '  and `transId` = :transId';
        }
        $st = $this->_driver->prepare($query);
        $st->bindValue(':date', $date, PDO::PARAM_STR);
        if($transId){
            $st->bindValue(':transId', $transId, PDO::PARAM_STR);
        }
        $st->bindValue(':priv', $priv, PDO::PARAM_STR);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function fetchAllDeptCreditPayments($date = '')
    {
        # code...
        $query = 'select * from `deptCreditPayments`';
        if($date){
            $query .= ' where `date` = :date';
        }
        $st = $this->_driver->prepare($query);
        if($date){ $st->bindValue(':date', $date, PDO::PARAM_STR); }
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function addDeptCreditPayment(Array $data)
    {
        # code...
        global $registry;
        $thisUser = unserialize($registry->get('session')->read('thisUser'));


        $st = $this->_driver->prepare('insert into `deptCreditPayments` ( date, creditDate, transId, amt, staffId, privilege ) values ( :date, :creditDate, :transId, :amt, :staffId, :privilege )');
        foreach ($data as $key => $value) {
            # code...
            if(in_array($key, array('amt', 'staffId', 'privilege')) !== false){
                $st->bindValue(':'.$key, $value, PDO::PARAM_INT);
            }else{
               $st->bindValue(':'.$key, $value, PDO::PARAM_STR);
            }
        }
        $st->execute();
        $id = $this->_driver->lastInsertId();

        return \Transaction::addNew(array(
                'date' => today(),
                'time' => now(),
                'transId' => $data['transId'],
                'transType' => 16,
                'src' => json_encode(array('tbl' => 'deptCreditPayments', 'id' => $id)),
                'details' => json_encode(array(
                                        'type' => 'Dept Credit Payment',
                                        'amt' => $data['amt'])),
                'staffId' => $thisUser->id,
                'privilege' => $thisUser->get('activeAcct')
                )) ? true : false;

    }

    public function fetchStaffCreditByTransId($transId)
    {
        # code...
        $st = $this->_driver->prepare('select * from `staffCredits` where `transId` = :transId');
        $st->bindValue(':transId', $transId, PDO::PARAM_STR);
        return  $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : array();
    }

    public function fetchStaffCreditPaymentsByTransId($transId)
    {
        # code...
        $st = $this->_driver->prepare('select * from `staffCreditPayments` where `transId` = :transId');
        $st->bindValue(':transId', $transId, PDO::PARAM_STR);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function addStaffCreditPayment(Array $data)
    {
        # code...
        $st = $this->_driver->prepare('insert into `staffCreditPayments` ( date, transId, staffId, amt ) values ( :date, :transId, :staffId, :amt )');
        foreach ($data as $key => $value) {
            # code...
            if($key == 'staffId' || $key == 'amt'){
                $st->bindValue(':'.$key, $value, PDO::PARAM_INT);
            }else{
                $st->bindValue(':'.$key, $value, PDO::PARAM_STR);
            }
        }
        return $st->execute() ? true : false;
    }

    public function fetchCashierCollections($date)
    {
        # code...
        $st = $this->_driver->prepare('select * from `cashierCollections` where `date` = :date');
        $st->bindValue(':date', $date, PDO::PARAM_STR);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function fetchStaffCredits(Array $data)
    {
        # code...

        $beginDate = $data['year'] . '-' . $data['month'] . '-01';
        $endDate = $data['year'] . '-' . $data['month'] . '-' . getMonthLastDate($data['month']);

        $query = 'select * from `staffCredits` where `date` between :beginDate and :endDate';
        if(isset($data['staffId']) && !is_null($data['staffId'])){
            $query .= ' and `staffId` = :staffId';
        }
        $st = $this->_driver->prepare($query);
        $st->bindValue(':beginDate', $beginDate, PDO::PARAM_STR);
        $st->bindValue(':endDate', $endDate, PDO::PARAM_STR);
        if(isset($data['staffId']) && !is_null($data['staffId'])){
            $st->bindValue(':staffId', $data['staffId'], PDO::PARAM_INT);
        }
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();

    }


    public function fetchStockPurchases($limit)
    {
        # code...
        $st = $this->_driver->prepare('select * from `stockPurchases` where `approved` != 2 order by `id` desc limit :limit');
        $st->bindValue(':limit', $limit, PDO::PARAM_INT);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function fetchStockPurchaseDetailsById($id)
    {
        # code...
        $st = $this->_driver->prepare('select * from `stockPurchases` where `id` = :id');
        $st->bindValue(':id', $id, PDO::PARAM_INT);
        return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : array();
    }

    public function fetchRejectedStockPurchases($limit)
    {
        # code...
        $st = $this->_driver->prepare('select * from `stockPurchases` where `approved` = 2 order by `id` desc limit :limit');
        $st->bindValue(':limit', $limit, PDO::PARAM_INT);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function fetchUnapprovedStockPurchases()
    {
        # code...
        $st = $this->_driver->prepare('select * from `stockPurchases` where `approved` = 0 order by `id` desc ');
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function deleteStockPurchase($id)
    {
        # code...
        $st = $this->_driver->prepare('delete from `stockPurchases` where `id` = :id');
        $st->bindValue(':id', $id, PDO::PARAM_INT);
        return $st->execute() ? true : false;
    }

    public function rejectStockPurchase($id)
    {
        # code...
        $st = $this->_driver->prepare('update `stockPurchases` set `approved` = 2 where `id` = :id');
        $st->bindValue(':id', $id, PDO::PARAM_INT);
        return $st->execute() ? true : false;
    }

    public function approveStockPurchase($id)
    {
        # code...
        $st = $this->_driver->prepare('update `stockPurchases` set `approved` = 1 where `id` = :id');
        $st->bindValue(':id', $id, PDO::PARAM_INT);
        return $st->execute() ? true : false;
    }

    public function fetchStockItemRemovals()
    {
        # code...
        $st = $this->_driver->prepare('select * from `stockItemRemovals` order by `id` desc');
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function fetchChairmanExpenses(Array $data)
    {
        # code...
        $beginDate = $data['year'] . '-' . $data['month'] . '-01';
        $endDate = $data['year'] . '-' . $data['month'] . '-' . getMonthLastDate($data['month']);

        $query = 'select * from `chairmanXpenses` where `date` between :beginDate and :endDate';
        $st = $this->_driver->prepare($query);
        $st->bindValue(':beginDate', $beginDate, PDO::PARAM_STR);
        $st->bindValue(':endDate', $endDate, PDO::PARAM_STR);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();

    }

    public function truncateTbl($tbl)
    {
        # code...
        $st = $this->_driver->prepare('truncate ' . $tbl);
        return $st->execute() ? true : false;
    }

    public function updateTbl($tbl)
    {
        # code...
        $st = $this->_driver->prepare('update `' . $tbl . '` set `qtyInStock` = 0');
        return $st->execute() ? true : false;

    }

    public function deleteFromTableByTransId($tbl, $transId)
    {
        # code...
        $st = $this->_driver->prepare('delete from  ' . $tbl . ' where transId = :transId');
        $st->bindValue(':transId', $transId, PDO::PARAM_STR);
        return $st->execute() ? true : false;
    }


    public function updateItemInStock(Array $data)
    {
        # code...
        $st = $this->_driver->prepare('update `' . $data['table'] . '` set `qtyInStock` = :qty where `itemId` = :itemId');
        $st->bindValue(':qty', $data['qty'], PDO::PARAM_INT);
        $st->bindValue(':itemId', $data['item'], PDO::PARAM_INT);
        return $st->execute() ? true : false;
    }

    public function updateItemInStock2(Array $data)
    {
        # code...
        $st = $this->_driver->prepare('update `' . $data['table'] . '` set `qtyInStock` = `qtyInStock` + :qty where
        `itemId` =
        :itemId');
        $st->bindValue(':qty', $data['qty'], PDO::PARAM_INT);
        $st->bindValue(':itemId', $data['item'], PDO::PARAM_INT);
        return $st->execute() ? true : false;
    }

    public function fetchTotalCreditPaymentsById($transId)
    {
        # code...
        $st = $this->_driver->prepare('select sum(`amt`) as `total` from `creditPayments` where `transId`  = :transId');
        $st->bindValue(':transId', $transId, PDO::PARAM_STR);
        $st->execute();
        $st->bindColumn('total', $total);
        $st->fetch(PDO::FETCH_OBJ);
        return $total;
    }

    public function getNotType($notType)
    {
        # code...
        $st = $this->_driver->prepare('select `type` from `notificationTypes` where `id` = :id');
        $st->bindValue(':id', $notType, PDO::PARAM_INT);
        $st->execute();
        $st->bindColumn('type', $type);
        $st->fetch(PDO::FETCH_ASSOC);
        return (is_null($type) || $type === false) ? '' : $type;
    }

    public function updateUserDetails($desc, $value, $userId)
    {
        # code...
        $query = 'update `staff` set ' . $desc . ' = :value where `id` = :id';
        $st = $this->_driver->prepare($query);
        $st->bindValue(':value', $value, PDO::PARAM_INT); #switch binding later ( depending on the desc )
        $st->bindValue(':id', $userId, PDO::PARAM_STR);
        return $st->execute() ? true : false;
    }

    public function insertShiftTimes(Array $data)
    {
        # code...
        $st = $this->_driver->prepare('insert into `shiftTimes` ( date, beginTime, endTime ) values ( :date, :beginTime, :endTime )');
        $st->bindValue(':date', today(), PDO::PARAM_STR);
        $st->bindValue(':beginTime', $data['beginTime'], PDO::PARAM_STR);
        $st->bindValue(':endTime', $data['endTime'], PDO::PARAM_STR);
        return $st->execute() ? true : false;
    }

    public function fetchShiftTimes($date = null)
    {
        $date = is_null($date) ? today() : $date;
        # code...
        $st = $this->_driver->prepare('select * from shiftTimes where date = :date');
        $st->bindValue(':date', $date, PDO::PARAM_STR);
        return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : null;
    }

    public function addNewStaff(Array $data)
    {
        # code...
        $st = $this->_driver->prepare('insert into `staff` ( name, deptId ) values (:name, :deptId )');
        $st->bindValue(':name',$data['name'], PDO::PARAM_STR);
        $st->bindValue(':deptId', $data['privilege'], PDO::PARAM_INT);
        $st->execute();
        return $this->_driver->lastInsertId();
    }

    public function fetchAllStaff()
    {
        # code...
        $st = $this->_driver->prepare('select * from `staff`');
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function fetchStaffDetails($staffId)
    {
        #
        $st = $this->_driver->prepare('select * from `staff` where id = :id');
        $st->bindValue(':id', $staffId, PDO::PARAM_INT);
        return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : null;
    }

    public function addStaffSubcharge(Array $data){
        $st = $this->_driver->prepare('insert into `subcharges` ( date, staffId, amt, reason) values ( :date,
        :staffId, :amt, :reason)');
        foreach ($data as $key => $value) {
            if(in_array($key, array('date', 'reason')) !== false){
                $st->bindValue(':'.$key, $value, PDO::PARAM_STR);
            }else{
                $st->bindValue(':'.$key, $value, PDO::PARAM_INT);
            }
        }
        return $st->execute() ? true : false;
    }

    public function fetchStaffSubcharges(Array $data)
    {
        # code...

        $beginDate = $data['year'] . '-' . $data['month'] . '-01';
        $endDate = $data['year'] . '-' . $data['month'] . '-' . getMonthLastDate($data['month']);

        $query = 'select * from `subcharges` where `date` between :beginDate and :endDate';
        if(isset($data['staffId']) && !is_null($data['staffId'])){
            $query .= ' and `staffId` = :staffId';
        }
        $st = $this->_driver->prepare($query);
        $st->bindValue(':beginDate', $beginDate, PDO::PARAM_STR);
        $st->bindValue(':endDate', $endDate, PDO::PARAM_STR);
        if(isset($data['staffId']) && !is_null($data['staffId'])){
            $st->bindValue(':staffId', $data['staffId'], PDO::PARAM_INT);
        }
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();

    }

    public function fetchStaffShortages(Array $data)
    {
        $beginDate = $data['year'] . '-' . $data['month'] . '-01';
        $endDate = $data['year'] . '-' . $data['month'] . '-' . getMonthLastDate($data['month']);

        $query = 'select * from `deptCredits` where `date` between :beginDate and :endDate';
        if(isset($data['staffId']) && !is_null($data['staffId'])){
            $query .= ' and `staffId` = :staffId';
        }
        $st = $this->_driver->prepare($query);
        $st->bindValue(':beginDate', $beginDate, PDO::PARAM_STR);
        $st->bindValue(':endDate', $endDate, PDO::PARAM_STR);
        if(isset($data['staffId']) && !is_null($data['staffId'])){
            $st->bindValue(':staffId', $data['staffId'], PDO::PARAM_INT);
        }
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();

    }

    public function fetchTotalStaffShortagesPayByTransId($transId)
    {
        # code...
        $st = $this->_driver->prepare('select sum(amt) as total from `deptCreditPayments` where `transId` = :transId');
        $st->bindValue(':transId', $transId, PDO::PARAM_STR);
        $st->execute();
        $st->bindColumn('total', $total);
        $st->fetch(PDO::FETCH_OBJ);
        return is_null($total) ? 0 : $total;
    }

    public function checkCashReturnsCollected($date, $priv){
        $st = $this->_driver->prepare('select id from cashierCollections where returnDate = :date and privilege =
        :priv');
        $st->bindValue(':date', $date, PDO::PARAM_STR);
        $st->bindValue(':priv', $priv,PDO::PARAM_INT);
        $st->execute();
        $st->bindColumn('id', $id);
        $st->fetch();
        return (is_null($id) || false === $id) ? false : true;
    }

    public function deleteUnpostedSaleById($id){
        $count = $this->_driver->exec('delete from sales where id = ' . $id);
        return $count == 1 ? true : false;
    }

    public function fetchSaleDetails($transId){
        $st= $this->_driver->prepare('select * from sales where transId = :transId limit 1');
        $st->bindValue(':transId', $transId, PDO::PARAM_STR);
        $st->execute();
        $res = $st->fetch();
        if(is_null($res) || false === $res){
            return null;
        }else{
            return json_encode(array(
                'guestType' => $res->guestType,
                'roomId' => $res->roomId
            ));
        }
    }


    public function executeTransQuery($tbl, $id){
		
        $query = 'select * from `' . $tbl . '` where id = :id';
        $st = $this->_driver->prepare($query);
        $st->bindValue(':id', $id, PDO::PARAM_INT);
		return $st->execute() ? $st->fetch() : array();
    }
	
	public function getCheckInReceptionist(){
		$date = date('Y-m-d');
		$query = 'select staffId from loggedInUsers where date = :date and privilege = 7 order by id desc limit 1';
		$st = $this->_driver->prepare($query);
        $st->bindValue(':date', $date, PDO::PARAM_STR);
		$st->execute();
		$st->bindColumn('staffId', $staffId);
		$st->fetch();
		return !is_null($staffId) ? $staffId : 0;
	}


    public function removeFromLoggedInUsers($staffId){

        $st = $this->_driver->prepare('delete from loggedInUsers where staffId = :staffId');
        $st->bindValue(':staffId', $staffId, PDO::PARAM_INT);
        return $st->execute() ? true : false;
    }





#end of class
}
