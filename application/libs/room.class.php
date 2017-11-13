<?php
//use application\libs\RoomDatabase as Db;

defined('ACCESS') || Error::exitApp();

/**
* 
*/
class Room extends FuniObject
{
	public $id;
	public $typeId;
	public $no; 
	public $type;
	public $price;
	public $status;
	public $checkInType;

	private $_db = null;


	function __construct($roomId)
	{
		# code...
		global $registry;
		$this->_db = $registry->get('roomDb');

		$data = $this->_db->fetchDetails($roomId);

		if(is_null($data) || false === $data){

		}else {

			$this->id = $data->id;
			$this->typeId = $data->roomTypeId;
			$this->no = $data->roomNo;
			$data = $this->_db->fetchType($this->typeId);
			//var_dump($data); die;
			if ( !empty($data) && $data !== false ) {
				$this->type = $data->type;
				$this->price = $data->price;
			}
			$this->_checkStatus();
		}
		
	}

	private function _getType()
	{
		# code...
		global $registry;
		return $this->_db->fetchType($this->roomTypeId);
	}

	private function _checkStatus(){
		global $registry;

		# check if room is bad
		$id = $this->_db->checkIfBad($this->id);
		
		
		# if Room is Bad
		if(!is_null($id) && $id !== false){
			$this->status =  'Bad';
			$this->checkInType = 'None';
		}else{

			$data = $this->_db->checkStatus($this->id);
			
			# if Room is occupied
			if(!is_null($data) && !empty($data)){

				$this->status = 'Occupied';

				#check checkIn Type
				$this->checkInType = strtolower($data->complimentary) == "no" ? 'Routine' : 'Complimentary';

			}else{
	    
		    	$this->status = 'Free';
		    	$this->checkInType = 'None';

	        }
	    }
	}




	/****************************
		STATIC FUNCTIONS
		*************************/

	public static function fetchFreeByType($roomType)
	{
		# code...
		global $registry;
		$db = $registry->get('roomDb');
		return $db->fetchFreeByType($roomType);
	}

	public static function fetchFree()
	{
		# code...
		global $registry;
		$db = $registry->get('roomDb');
		return $db->fetchFree();
	}

	public static function fetchByType($roomType)
	{
		# code...
		global $registry;
		$db = $registry->get('roomDb');
		return $db->fetchByType($roomType);
	}

	public static function fetchAll()
	{
		# code...
		global $registry;
		$db = $registry->get('roomDb');
		return $db->fetchAll();
	}

	public static function checkAvailablity($roomId, $beginDate, $endDate)
	{
		# code...
		global $registry;
		$db = $registry->get('roomDb');
		return $db->checkAvailablity($roomId, $beginDate, $endDate);
	}

	public static function fetchByNo($roomNo)
	{
		# code...
		global $registry;
		$db = $registry->get('roomDb');
		return $db->fetchByNo($roomNo);
	}

	public static function getOccupied($distinct = false)
	{
		# code...
		global $registry;
		$db = $registry->get('roomDb');
		return $db->getOccupied($distinct);
	}

	public static function getBad()
	{
		# code...
		global $registry;
		$db = $registry->get('roomDb');
		return $db->getBad();
	}

	public static function removeBad($roomId)
	{
		# code...
		global $registry;
		$db = $registry->get('roomDb');
		return $db->removeBad($roomId);
	}

	public static function getCategories()
	{
		# code...
		global $registry;
		$db = $registry->get('roomDb');
		return $db->getCategories();
	}

	public static function addBad(Array $data)
	{
		# code...
		global $registry;
		$db = $registry->get('roomDb');
		return $db->addBad($data);
	}

	public static function getAppReserved()
	{
		# code...
		global $registry;
		$db = $registry->get('roomDb');
		return $db->getAppReserved();
	}

	public static function getWebReserved()
	{
		# code...
		global $registry;
		$db = $registry->get('roomDb');
		return $db->getWebReserved();
	}

	public static function exemptFromAutoBill($roomId, $date, $staffId)
	{
		# code...
		global $registry;
		$db = $registry->get('roomDb');
		return $db->exemptFromAutoBill($roomId, $date, $staffId);
	}

	public static function isExemptedFromAutoBilling($roomId, $date)
	{
		# code...
		global $registry;
		$db = $registry->get('roomDb');
		return $db->checkExemptedFromAutoBilling($roomId, $date);
	}

	public static function fetchAutoBillExemptions()
	{
		# code...
		global $registry;
		return $registry->get('roomDb')->fetchAutoBillExemptions();
	}

	public static function inLateCheckOutList($roomId, $date)
	{
		# code...
		global $registry;
		$db = $registry->get('roomDb');
		return $db->inLateCheckOutList($roomId, $date);
	}

	public static function addToLateCheckOut($roomId, $date, $time, $staffId)
	{
		# code...
		global $registry;
		$db = $registry->get('roomDb');
		return $db->addToLateCheckOut($roomId, $date, $time, $staffId);
	}


	# End of Class
}