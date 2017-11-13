<?php
/**
* Will Handle Pagination
* Culled From Pagination Class of My previous prjts
*
*/

//namespace \Libs\Core;

defined('ACCESS') || Error::exitApp();

class Paginator extends FuniObject{
	 protected $_currentPage;
	 protected $_perPage;
	 protected $_totalCount;
 
	public function __construct(Array $options){
		$this->_currentPage = isset($options['page']) ? (int)$options['page'] : 1;
		$this->_perPage = isset($options['perPage']) ? (int)$options['perPage'] : 10;
		$this->_totalCount = isset($options['totalCount']) ? (int)$options['totalCount'] : 0;
	}
	 
	public function offset(){
	 	return ($this->_currentPage - 1) * $this->_perPpage;
	}
	 
	public function totalPages(){
		 return ceil($this->_totalCount/$this->_perPage);
	}
	public function previousPage(){
		return $this->currentPage - 1;
	}
	public function nextPage(){
		return $this->currentPage + 1;
	}
	public function hasPreviousPage(){
		return $this->previousPage() >= 1 ? true : false;
	}
	public function hasNextPage(){
		return $this->nextPage() <= $this->totalPages() ? true: false;
	}
	
}