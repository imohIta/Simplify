<?php
namespace application\libs;
use \PDO;
use core\libs\Database as Db;
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class PaymentDatabase extends Db{ 
	
	public function fetchPaymentById($id)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchPaymentById(:id)');
		$st->bindValue(':id', $id, PDO::PARAM_INT);
		return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : null;
	}

	public function fetchDistinctGuestPayments($date)
	{
		# code...
		$st = $this->_driver->prepare('select * from `guestPayments` where `date` = :date group by `guestId`');
		$st->bindValue(':date', $date, PDO::PARAM_STR);
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
	}


	public function delete($transId)
    {
        # code...
        $st = $this->_driver->prepare('delete from `guestPayments` where `transId` = :transId');
        $st->bindValue(':transId', $transId, PDO::PARAM_STR);
        return $st->execute() ? true : false;
    }

#end of class	 
}