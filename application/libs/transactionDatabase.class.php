<?php
namespace application\libs;
use \PDO;
use core\libs\Database as Db;
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class TransactionDatabase extends Db{

	public function getTransactionById($transId)
    {
    	# code...
    	$st = $this->_driver->prepare('CALL sp_getTransactionById(:transId)');
    	$st->bindValue(':transId', $transId, PDO::PARAM_STR);
    	return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : null;
    }

	public function getTransactionByAutoId($autoId)
	{
		# code...
		$st = $this->_driver->prepare('select * from `transactions` where id = :id');
    	$st->bindValue(':id', $autoId, PDO::PARAM_INT);
    	return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : null;
	}



    public function addNew(Array $data)
    {
        # code...
        $st = $this->_driver->prepare('CALL sp_insertTransaction(:date, :time, :transId, :transType, :src, :details, :staffId, :privilege )');
        foreach ($data as $key => $value) {
            if(in_array($key, array('transType', 'staffId', 'privilege')) !== false){
                $st->bindValue(':'.$key, $value, PDO::PARAM_INT);
            }else{
                $st->bindValue(':'.$key, $value, PDO::PARAM_STR);
            }
        }
        return $st->execute() ? true : false;
    }

     public function updateDetails(Array $params)
    {
        # code...

        $st = $this->_driver->prepare('CALL sp_updateTransactionDetails(:transId, :details)');
        foreach ($params as $key => $value) {
            # code...
            $st->bindValue(':'.$key, $value, PDO::PARAM_STR);
        }
        return $st->execute() ? true : false;
    }

    public function fetchUserTransactions($userId, $priv, $date, $time1, $time2)
    {
        # code...
		global $registry;
		$session = $registry->get('session');
		$thisUser = unserialize($session->read('thisUser'));
        //var_dump(strtotime("today 8am"), $time1);
        //var_dump(time(), $time1, $time2, $time1 < time()); die;
		
		if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5,6))){
			 $st = $this->_driver->prepare('select * from `transactions` where `privilege` = :priv and (`time` between :startTime and :endTime);');
		}else{
			# if ordinary user...fetch only his/her transaction
			$st = $this->_driver->prepare('select * from `transactions` where `staffId` = :userId and privilege = :priv and ( `time` between :startTime and :endTime)' );
		}


        if(!$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5,6))) {
            $st->bindValue(':userId', $userId, PDO::PARAM_INT);
        }
        $st->bindValue(':priv', $priv, PDO::PARAM_INT);
        //$st->bindValue(':date', $date, PDO::PARAM_STR);
        $st->bindValue(':startTime', $time1, PDO::PARAM_STR);
        $st->bindValue(':endTime', $time2, PDO::PARAM_STR);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : false;
    }

    public function fetchDesc($type)
    {
        # code...
        $st = $this->_driver->prepare('CALL sp_getTransDesc(:transType)');
        $st->bindValue(':transType', $type, PDO::PARAM_INT);
        $st->execute();
        $st->bindColumn('transType', $transType);
        $st->fetch(PDO::FETCH_ASSOC);
        return $transType;
    }

    public function applyForReversal(Array $data)
    {
        # check if the transaction is already added to transaction reversal table to prevent duplicate entry
        $st = $this->_driver->prepare('insert into `transactionReversals` ( transId, reversalInfo, transInfo, details, status ) values ( :transId, :reversalInfo, :transInfo, :details, :status )');
        foreach ($data as $key => $value) {
            if($key == 'status'){
                $st->bindValue(':'.$key, $value, PDO::PARAM_INT);
            }else{
                $st->bindValue(':'.$key, $value, PDO::PARAM_STR);
            }
        }
        return $st->execute() ? true : false;
    }

    public function executeQuery(Array $data)
    {
        # code...
        $st = $this->_driver->prepare($data['query']);
        foreach ($data['values'] as $key => $value) {
            # code...
            if($key == 'privilege'){
                $st->bindValue(':'.$key, $value, PDO::PARAM_INT);
            }else{
                $st->bindValue(':'.$key, $value, PDO::PARAM_STR);
            }
        }
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function fetchSrcDetails($tbl, $id)
    {
        # code...
        $st = $this->_driver->prepare('select * from ' . $tbl . ' where `id` = :id');
        $st->bindValue(':id', $id, PDO::PARAM_INT);
        return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : array();
    }

    public function fetch($type, $date)
    {
        # code...
        $query = 'select * from `transactions` where `transType` = :type';
        if($date){
            $query .= ' and `date` = :date';
        }
        $st = $this->_driver->prepare($query);
        $st->bindValue(':type', $type, PDO::PARAM_INT);
        if($date){ $st->bindValue(':date', $date, PDO::PARAM_STR); }
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function fetchForFinancialReport($beginDate, $endDate)
    {
        # code...
        $st = $this->_driver->prepare('select * from `transactions` where `transType` in (2,3,7,9,10,16) and `date` between :beginDate and :endDate');
        $st->bindValue(':beginDate', $beginDate, PDO::PARAM_STR);
        $st->bindValue(':endDate', $endDate, PDO::PARAM_STR);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function delete($transId)
    {
        # code...
        $st = $this->_driver->prepare('delete from `transactions` where `transId` = :transId');
        $st->bindValue(':transId', $transId, PDO::PARAM_STR);
        return $st->execute() ? true : false;
    }

    public function fetchReversals($limit)
    {
        # code...
        $st = $this->_driver->prepare('select * from `transactionReversals` order by `id` desc limit :limit');
        $st->bindValue(':limit', $limit, PDO::PARAM_INT);
        return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
    }

    public function fetchReversalById($id)
    {
        # code...
        $st = $this->_driver->prepare('select * from `transactionReversals` where `id` = :id');
        $st->bindValue(':id', $id, PDO::PARAM_INT);
        return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : array();
    }

    public function updateReversalInfo($reversalInfo, $status, $id)
    {
        # code...
        $st = $this->_driver->prepare('update `transactionReversals` set `reversalInfo` = :reversalInfo, `status` = :status where `id` = :id');
        $st->bindValue(':reversalInfo', $reversalInfo, PDO::PARAM_STR);
        $st->bindValue(':status', $status, PDO::PARAM_INT);
        $st->bindValue(':id', $id, PDO::PARAM_INT);
        return $st->execute() ? true : false;
    }



#end of class
}
