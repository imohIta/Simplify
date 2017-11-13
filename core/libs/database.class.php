<?php
namespace core\libs;
use \PDO;
use \Error;
/**
*
*
*/
defined('ACCESS') || Error::exitApp();



class Database extends \FuniObject{

	/**
     * Allows multiple database connections
     */
    protected $_connections = array();

    /**
     * Tells the DB object which connection to use
     * setActiveConnection($connectionName) allows us to change this
     */
    protected $_activeConnection = '';

    /**
     * Data which has been prepared and then "saved for later"
     */
    protected $_queryCache = array();

    /**
     * Queries which have been executed and then "saved for later"
     */
    protected $_dataCache = array();

	 /**
     * Cache Engine to cache Database results
	 * Should be passed as a paramater to the consructor
     */
	protected $_cacheEngine;

	protected $_driver;


    /**
	 * @params Array of Db Configuration Options
     * Could Also Pass a cache object as argument in the future
     */
    public function __construct( Array $options){

         //create new Database Connection
		 $this->newConnection($options);
    }

    /**
     * Create a new database connection
	 * If active is true..sets this connection as the active connection
     * @param String database hostname
     * @param String database username
     * @param String database password
     * @param String database we are using
     * @return int the id of the new connection
     */
    public function newConnection( Array $options ){

		 $driver = isset($options['driver']) ? $options['driver'] : 'mysql';
		 $host = isset($options['host']) ? $options['host'] : 'localhost';
		 $user = isset($options['user']) ? $options['user'] : 'root';
		 $pwd = isset($options['password']) ? $options['password'] : 'root';
		 $dbName = isset($options['dbName']) ? $options['dbName'] : 'kelvic_hms';
		 $active = isset($options['active']) ? $options['active'] : true;


		 //make Sure DbName is set
		 if(!$dbName){
			Error::throwException('Database name not Set');
		 }

         $dsn = $driver . ":host=" . $host . ";dbname=" . $dbName . ";charset=utf8";

		 try{
			$this->_connections[$dbName] = new PDO($dsn, $user, $pwd );
			$this->_connections[$dbName]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		    //$this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		    $this->_connections[$dbName]->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY , true);
		    $this->_connections[$dbName]->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

		 }catch(PDOException $e){
			echo $e->getMessage();
			die;
		 }
		//if active is true..set this connection as the active connection
		if($active){
			$this->setActiveConnection($dbName);
		}
    }

    /**
     * Close the active connection
     * unset connection from connections array
	 * Only Use this when u want to totally destroy the connection
     */
    public function closeConnection(){
        $this->_connections[$this->_activeConnection] = null;
		unset($this->_connections[$this->_activeConnection]);
    }

    /**
     * Change which database connection is actively used for the next operation
     * @param int the new connection id
     * @return void
     */
    public function setActiveConnection( $conn ){
        $this->_activeConnection = $conn;
        $this->_driver = $this->_connections[$this->_activeConnection];
    }

	/**
     * Execute a query string
     * @param String the query
     * @return void
     */
	 public function query( $queryStr, Array $parambinding = array(), $fetchType = 'single' ){
        //Use Active Connection to prepare PDO Statement
		$st = $this->_connections[$this->_activeConnection]->prepare($queryStr);
		if(!empty($parambinding)){
	        foreach($parambinding as $key => $value){
			   $st->bindValue($key, $value);
			}
		}
		if($fetchType == 'single'){
			return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : array();
		}else{
			return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
		}
		//to be contd..

    }

	 /**
     * Insert records into the database
     * @param Array Options
     * option will contain query, table ( is Stored Procedure is not Used) , Array data
	 * @param usingSp ( Stored Procedure ) .. default = true
     * Always set UsingSp to false when calling this function without Stored Procedures
     */

	public function insert( Array $options, $usingSp = true ){

		if(empty($options['params'])){
			Error::throwException('Parameters Array must not be Empty');
		}

		// setup some variables for Query, fields and values
        $fields  = "";
        $values = "";

		// populate them
		foreach ($options['params'] as $f => $v){
		   $fields  .= " `$f`,";
			//$values .= ( is_numeric( $v ) && ( intval( $v ) == $v ) ) ? $v."," : "'$v',"; //use this if not using pdo
			$values .= " :$f ,";
		}
		// remove our trailing ,
		$fields = substr($fields, 0, -1);
		// remove our trailing ,
		$values = substr($values, 0, -1);

		//if using a stored procedure
		if($usingSp){

			if(!isset($options['query'])){
				Error::throwException('Query String must not be empty where using a Stored Procedure');
			}

			//Build query string
			$queryStr = $options['query'] . '( ' . $values . ' )';

		}else{

			if(!isset($options['table'])){
				Error::throwException('Table Name must not be Empty when not using a Stored Procedure...Set Sp to True if using a Stored Procedure or Enter Table name if you are not');
			}

			//Build query string
			$queryStr = "INSERT INTO `$options[table]` ({$fields}) VALUES({$values})";

		}

		$st = $this->_connections[$this->_activeConnection]->prepare($queryStr);
        //bind Parameters
		foreach($options['params'] as $field => $value){
		   $st->bindValue( ':' .$field, $value);
		}
		$st->execute();
		$st = NULL;

		return true;

	}


	 /**
     * Delete records from the database
     * @param String the table to remove rows from
     * @param String the condition for which rows are to be removed
     * @param int the number of rows to be removed
     * @return void
     */
    /*public function delete( Array $queryOptions, $usingSp = true ){

		//if using a Stored Preocedure
		if($usingSp){
			//if query was not passes...thow exception
			if( !isset($queryOptions['query'])){
				Error::throwException('Query String must not be empty where using a Stored Procedure');
			}

			//build query String
			$queryStr = $queryOptions['query'];

		}else{ // if not using a Stored Preocedure

			//if table option not provided
			if(!isset($queryOptions['table'])){
				Error::throwException('Table Name Option must not be empty where not using a Stored Procedure');
			}

			//if Condition option not provided
			if(!isset($queryOptions['condition'])){
				Error::throwException('Delete Condition Option must not be empty where not using a Stored Procedure');
			}

			$limit = isset($queryOptions['limit']) ? ' LIMIT ' . $queryOptions['limit'] : '';

			//build query String
			$queryStr = "DELETE FROM {$queryOptions['table']} WHERE {$queryOptions['condition']} {$limit}";

		}*/

		//echo '<br />' . $queryStr; die;

		//execute query
		// $this->_connections[$this->_activeConnection]->exec($queryStr);
		// return true;
  //   }

    /**
     * Update records in the database
     * @param String the table
     * @param array of changes field => value
     * @param String the condition
     * @return bool
     */
     public function update( $tbl, Array $changes, Array $conditions)
 	{
 		//update tableName set field1= value1, feild2 = value2 where cond1 = condValu1 and cond2 = conValue2
 		# check if dbName exist
 		$this->_checkTableExist($tbl);

 		if(empty($changes)){
 			AppError::throwException('update changes Array must not be Empty','500');
 		}

 //		if(empty($conditions)){
 //			throw new \Mf_Core\Upload\Exception('Parameters Array must not be Empty');
 //		}


 		//if table option not provided
 		if($tbl == ''){
 			AppError::throwException('Table Name Option must not be empty');
 		}

 		//if Condition option not provided
 		if(empty($changes)){
 			AppError::throwException('Changes must not be empty when using update method');
 		}


 		//build query
 		$queryStr = "UPDATE " . $tbl . " SET ";
 		foreach( $changes as $field => $value )
 		{
 			$queryStr .= "`" . $field . "`='{$value}',";
 		}

 		// remove our trailing ,
 		$queryStr = substr($queryStr, 0, -1);
 		if(!empty($conditions)) {

 			$queryStr .= ' where ';

 			$counter = 1;
 			foreach ( $conditions as $fieldName => $value ) {
 				if(ctype_digit($value)) {
 					$queryStr .= $fieldName . ' = ' . $value;
 				}else{
 					$queryStr .= $fieldName . ' = "' . $value . '"';
 				}

 				if ( count($conditions) > 1 && $counter < count($conditions) ) {
 					$queryStr .= ' and ';
 				}

 				$counter++;
 			}
 		}


 		if($this->_driver->exec( $queryStr )){
 			return true;
 		}
 		return false;


 	}





    /**
     * Preapre a query and Store the query cache for processing later
     * @param String the query string
	 * @param String the query title
     * @return null
     */
    public function cacheQuery( $queryStr, $queryTitle ){
        $st = $this->_connections[$this->_activeConnection]->prepare($queryStr);
        $this->_queryCache[$queryTitle] = $st;
		$st = NULL;
    }


    /**
     * Store some data in a cache for later
     * @param array the data
     * @return int the pointed to the array in the data cache
     */
    public function cacheData( $key, $data )
    {
        $this->_dataCache[$key] = $data;
        return true;
    }

    /**
     * Get data from the data cache
     * @param int data cache pointed
     * @return array the data
     */
    public function getData( $key )
    {
		if(isset($this->_dataCache[$key])){
        	return $this->dataCache[$key];
		}
		return NULL;
    }

    /**
     * Deconstruct the object
     * close all of the database connections
     */
    /*public function __deconstruct()
    {
        foreach( $this->connections as $connection )
        {
            $connection->close();
        }
    }*/
}
