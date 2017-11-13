<?php

namespace core\libs;


defined('ACCESS') || Error::exitApp();

/*
* Handle all Logging
* Will receive the option of either a file or Database as Store
*
*/

class Logger extends \FuniObject{

	protected $_db;
	
	public function __construct($db)
	{
		# code...
		global $registry;
		$this->_db = $db;
	}
	
}
