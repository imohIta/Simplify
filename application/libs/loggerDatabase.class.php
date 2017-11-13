<?php
namespace application\libs;
use \PDO;
use core\libs\Database as Db;
/**
*
*
*/
defined('ACCESS') || AppError::exitApp();

class LoggerDatabase extends Db{
	
	public function logNotification(Array $data)
	{
		$data['targetStaffId'] = isset($data['targetStaffId']) ? $data['targetStaffId'] : 0;

		$st = $this->_driver->prepare('CALL sp_notify (:date, :time, :notType, :details, :staffId, :targetStaffId)');
		foreach ($data as $key => $value) {
			# code...
			if(in_array($key, array('notType','staffId','targetStaffId')) !== false){
				$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
			}else{
				$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
			}
		}
		return $st->execute() ? true : false;

	}

	public function logUserLogin(Array $data){
		$st = $this->_driver->prepare('insert into loggedInUsers( date, time, staffId, privilege ) values (:date, :time, :staffId, :privilege)');

		foreach ($data as $key => $value) {
			# code...
			if(in_array($key, array('staffId','privilege')) !== false){
				$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
			}else{
				$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
			}
		}
		return $st->execute() ? true : false;
	}


#end of class	
}