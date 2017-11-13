<?php
use application\libs\ImpressDatabase as Db;
defined('ACCESS') || Error::exitApp();

/**
* 
*/
class Impress extends FuniObject
{
	public static $bal;
	

	private $_db = null; 

	function __construct() 
	{ 
		# code...  
		global $registry;
		$this->_db = $registry->get('impressDb');

		self::$bal = $this->_db->fetchCurrentBal();
	}

	public function fetchPayIns($date = '')
	{
		# code...
		return $this->_db->fetchPayIns($date);
	}

	public function fetchExpenses($date='')
	{
		# code...
		return $this->_db->fetchExpenses($date);
	}

	

	

	public function addExpenses(Array $values)
	{
		# code...
		$values['impressBal'] = self::$bal - $values['amt'];
		$this->_db->addExpenses($values);
		$this->_db->reduceBal($values['amt']);
		self::$bal = $this->_db->fetchCurrentBal();
	}

	public function addPayIn(Array $data)
	{
		# code...
		$this->_db->addPayIn($data, self::$bal + $data['amt']);
		$this->_db->increaseBal($data['amt']);
		self::$bal = $this->_db->fetchCurrentBal();
	}


	/**************************************
			STATIC FUNCTIONS
	**************************************/

	public static function fetchTrend($date='')
	{
		# code...
		global $registry;
		$date = ($date) ? $date : today();
		return $registry->get('impressDb')->fetchTrend($date);
	}

	public static function fetchBalBroughtForward($date='')
	{
		# code...
		global $registry;
		$date = ($date) ? $date : today();
		return $registry->get('impressDb')->fetchBalBroughtForward($date);
	}

	public static function fetchPayInById($id)
	{
		# code...
		global $registry;
		return $registry->get('impressDb')->fetchPayInById($id);
	}

	public static function fetchPayInsForDateRange($month, $year)
	{
		# code...
		global $registry;
		$year = filter_var($year, FILTER_SANITIZE_NUMBER_INT);
		$month = filter_var($month, FILTER_SANITIZE_NUMBER_INT);

		$beginDate = $year . '-' . $month . '-01';
		$endDate = $year . '-' . $month . '-' . getMonthLastDate($month);

		return $registry->get('impressDb')->fetchPayInsForDateRange($beginDate, $endDate);
	}

	public static function fetchExpensesForDateRange($month, $year)
	{
		# code...
		global $registry;
		$year = filter_var($year, FILTER_SANITIZE_NUMBER_INT);
		$month = filter_var($month, FILTER_SANITIZE_NUMBER_INT);

		$beginDate = $year . '-' . $month . '-01';
		$endDate = $year . '-' . $month . '-' . getMonthLastDate($month);

		return $registry->get('impressDb')->fetchExpensesForDateRange($beginDate, $endDate);
	}

	public static function fetchExpensesById($id)
	{
		# code...
		global $registry;
		return $registry->get('impressDb')->fetchExpensesById($id);
	}

	public static function addNewCategory($catName)
	{
		# code...
		global $registry;
		return $registry->get('impressDb')->addNewCategory(ucwords($catName));
	}

	public static function fetchCategories()
	{
		# code...
		global $registry;
		return $registry->get('impressDb')->fetchCategories();
	}

	public static function getCategoryName($catId)
	{
		# code...
		global $registry;
		return $registry->get('impressDb')->getCategoryName($catId);
	}

	public static function totalPayIns($date)
	{
		global $registry;
		return $registry->get('impressDb')->totalPayIns($date);
	}

	public static function totalExpenses($date)
	{
		global $registry;
		return $registry->get('impressDb')->totalExpenses($date);
	}


	
	


#end of class
}



