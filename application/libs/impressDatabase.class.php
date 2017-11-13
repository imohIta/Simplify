<?php
namespace application\libs;
use \PDO;
use core\libs\Database as Db;
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class ImpressDatabase extends Db{ 
	
	public function fetchCurrentBal()
	{ 
		# code...
		$st = $this->_driver->prepare('select `amt` from `impressAcct` where `id` = :id');
		$st->bindValue(':id', 1, PDO::PARAM_INT);
		$st->execute();
		$st->bindColumn('amt', $amt);
		$st->fetch(PDO::FETCH_ASSOC);
		return $amt;
	} 

	public function fetchPayIns($date)
	{
		# code...
		$query = 'select * from `impressPayIns`';
		if($date){
			$query .= ' where `date` = :date';
		}
		$st = $this->_driver->prepare($query);
		if($date){ $st->bindValue(':date', $date, PDO::PARAM_STR); }
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
	}

	public function totalPayIns($date)
	{
		# code...
		$query = 'select sum(amt) as total from `impressPayIns` where `date` = :date';
		$st = $this->_driver->prepare($query);
		$st->bindValue(':date', $date, PDO::PARAM_STR); 
		$st->execute();
		$st->bindColumn('total', $total);
		$st->fetch(PDO::FETCH_ASSOC);
		return is_null($total) ? 0 : $total;
	}

	public function fetchPayInsForDateRange($beginDate, $endDate)
	{
		# code...
		$query = 'select * from `impressPayIns` where `date` between :beginDate and :endDate';
		$st = $this->_driver->prepare($query);
		$st->bindValue(':beginDate', $beginDate, PDO::PARAM_STR); 
		$st->bindValue(':endDate', $endDate, PDO::PARAM_STR);
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
	}

	public function fetchTrend($date)
	{
		# code...
		$st = $this->_driver->prepare('select * from `impressTrend` where `date` >= :date');
		$st->bindValue(':date', $date, PDO::PARAM_STR); 
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
	}

	public function fetchBalBroughtForward($date)
	{
		# code...
		$st = $this->_driver->prepare('select * from `impressTrend` where `date` < :date order by `id` desc limit 0,1');
		$st->bindValue(':date', $date, PDO::PARAM_STR); 
		return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : array();
	}

	public function fetchExpenses($date)
	{
		# code...
		$query = 'select * from `impressExpenditures`';
		if($date){
			$query .= ' where `date` = :date';
		}
		$st = $this->_driver->prepare($query);
		if($date){ $st->bindValue(':date', $date, PDO::PARAM_STR); }
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
	}

	public function totalExpenses($date)
	{
		# code...
		$query = 'select sum(amt) as total from `impressExpenditures` where `date` = :date';
		$st = $this->_driver->prepare($query);
		$st->bindValue(':date', $date, PDO::PARAM_STR); 
		$st->execute();
		$st->bindColumn('total', $total);
		$st->fetch(PDO::FETCH_ASSOC);
		return is_null($total) ? 0 : $total;
	}

	public function fetchExpensesForDateRange($beginDate, $endDate)
	{
		# code...
		$query = 'select * from `impressExpenditures` where `date` between :beginDate and :endDate';
		$st = $this->_driver->prepare($query);
		$st->bindValue(':beginDate', $beginDate, PDO::PARAM_STR); 
		$st->bindValue(':endDate', $endDate, PDO::PARAM_STR);
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
	}

	public function fetchCategories()
	{
		# code...
		$st = $this->_driver->prepare('select * from `impressCategories`');
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
	}

	public function addExpenses(Array $data)
	{
		# code...
		//var_dump($data); die;
		$st = $this->_driver->prepare('insert into `impressExpenditures` ( date, category, details, amt, impressBal ) values ( :date, :category, :details, :amt, :impressBal )');
		foreach ($data as $key => $value) {
			# code...
			if($key == 'amt' || $key == 'category' || $key == 'impressBal'){
				$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
			}else{
				$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
			}
		}
		
		$st->execute();
		$id = $this->_driver->lastInsertId();
		return $this->insertTrend(array(
			'date' => today(),
			'type' => 2,
			'typeId' => $id
			)) ? true : false;
	}

	public function insertTrend(Array $data)
	{
		# code...
		$st = $this->_driver->prepare('insert into `impressTrend` ( date, type, typeId ) values ( :date, :type, :typeId )');
		foreach ($data as $key => $value) {
			# code...
			if($key == 'date'){
				$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
			}else{
				$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
			}
		}
		return $st->execute() ? true : false;
	}

	public function reduceBal($amt)
	{
		# code...
		$st = $this->_driver->prepare('update `impressAcct` set `amt` = `amt` - :amt where `id` = :id');
		$st->bindValue(':amt', $amt, PDO::PARAM_INT);
		$st->bindValue(':id', 1, PDO::PARAM_INT);
		return $st->execute() ? true : false;
	}

	public function increaseBal($amt)
	{
		# check if impress bal exist at all
		$st = $this->_driver->prepare('select id from impressAcct where id = 1');
		$st->execute();
		$st->bindColumn('id', $id);
		$st->fetch();
		if(!is_null($id) && false !== $id){
		
			# update impress
			$st = $this->_driver->prepare('update `impressAcct` set `amt` = `amt` + :amt where `id` = :id');
			$st->bindValue(':amt', $amt, PDO::PARAM_INT);
			$st->bindValue(':id', 1, PDO::PARAM_INT);
			return $st->execute() ? true : false;
			
		}else{
			$st = $this->_driver->prepare('insert into `impressAcct`( amt) values( :amt )');
			$st->bindValue(':amt', $amt, PDO::PARAM_INT);
			return $st->execute() ? true : false;
		}
	}

	public function addNewCategory($catName)
	{
		# code...
		$st = $this->_driver->prepare('insert into `impressCategories` ( type ) values ( :type )');
		$st->bindValue(':type', $catName, PDO::PARAM_STR);
		return $st->execute() ? true : false;
	}

	public function addPayIn(Array $data, $bal)
	{
		# code...
		$st = $this->_driver->prepare('insert into `impressPayIns` ( date, src, amt, impressBal ) values ( :date, :src,  :amt, :bal )');
		foreach ($data as $key => $value) {
			# code...
			if($key == 'amt'){
				$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
			}else{
				$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
			}
		}
		$st->bindValue(':bal', $bal, PDO::PARAM_INT);
		$st->execute();
		$id = $this->_driver->lastInsertId();
		return $this->insertTrend(array(
			'date' => today(),
			'type' => 1,
			'typeId' => $id
			)) ? true : false;
	}

	public function fetchPayInById($id)
	{
		# code...
		$st = $this->_driver->prepare('select * from `impressPayIns` where `id` = :id');
		$st->bindValue(':id', $id, PDO::PARAM_INT);
		return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : array();
	}

	public function fetchExpensesById($id)
	{
		# code...
		$st = $this->_driver->prepare('select * from `impressExpenditures` where `id` = :id');
		$st->bindValue(':id', $id, PDO::PARAM_INT);
		return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : array();
	}

	public function getCategoryName($catId)
	{
		# code...
		$st = $this->_driver->prepare('select `type` from `impressCategories` where `id` = :id');
		$st->bindValue(':id', $catId, PDO::PARAM_INT);
		$st->execute();
		$st->bindColumn('type', $name);
		$st->fetch(PDO::FETCH_ASSOC);
		return $name;
	}
	

#end of class	 
}