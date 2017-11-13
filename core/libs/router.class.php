<?php
/*
*
*
*ACTIONS
*set controller
*set action
*Extract andset parameters
*/
use \Core\Error;

defined('ACCESS') or Error::exitApp();

class Router extends FuniObject{

	 protected $_uri;
	 protected $_params = array();
	 protected $_controller;
	 protected $_method = 'execute';
	 protected $_component = 'Login';
	 protected $_path;

	 protected $_model, $view;
	 private $_initialized = false;

	 //private static $_instance = NULL;

	 public function __construct($uri, $path = '/apps/funi1.0/public/'){
	 	//echo $uri; die;
		//set default Mthd
		$this->_params['mthd'] = 'render';

		if(!is_array($uri)){
			$this->_uri = filter_var($uri, FILTER_SANITIZE_URL);
		}else{
			$this->_uri = $uri;
		}
		$this->_path = $path;
	 }

	 public function initialize(){
		 //Create Default MVC...( in this case - Dashboard )
		$this->_createMVC();
		$this->_initialized = true;
	 }

	 public function route(){
		 //check if router object has been initializes
		 //if not..initialize
		 if(!$this->_initialized){  $this->initialize(); }


		/*
		* Check if the Passed uri is an array
		* This is jst to make the router more robust
 		*/
		if(is_array($this->_uri)){

			$this->_component = ucfirst($this->_uri[0]);

			//set method to call
			$this->_params['mthd'] = filter_var($this->_uri[1], FILTER_SANITIZE_STRING);

			//Set the Remainder of the uri array to the Params Array
			$this->_params[] = array_splice($this->_uri, 2);

			return;

		}

		/**
		 * If the Passed Uri is in url format
		 * take out the base path from the server uri
		 * so our uri will rightly begin with the component controller
		*/
		$this->_uri = str_replace($this->_path,'',$this->_uri);
		$str = parse_url($this->_uri);
		$path = $str['path'];
		$query = isset($str['query']) ? $str['query'] : "";

		$list = explode("/", $path);

		if(isset($list[0]) && $list[0] != ""){
			$this->_component = filter_var(ucfirst($list[0]), FILTER_SANITIZE_STRING);
		}

		if(isset($list[1]) && $list[1] != ""){
			$this->_params['mthd'] = filter_var($list[1], FILTER_SANITIZE_STRING);
		}

		//set the remainder after removing the controller and the mthd as parameters
		$this->_params[] = array_slice($list,2);

		if($query != ""){
			$chunks = explode('&', $query);
			foreach($chunks as $chunk){
				list($key,$value) = explode('=', $chunk);
				if(ctype_digit($value)){
					$this->_params[] = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
				}else{
					$this->_params[] = filter_var($value, FILTER_SANITIZE_STRING);
				}
			}
		}
		$this->_execute();

	 }

	 public function getParam($key){
		 if(!isset($this->_params[$key])){
			//throw new Exception('Paramater Key Exceeds Limit');
			return NULL;
		 }
		return $this->_params[$key];
	 }


	private function _execute(){
		if(!class_exists($this->_component .'Controller')){
			\Core\Error::throwException('Invalid Url', '404');
		}

		//check if the component is not the default...ie if another url has been passed
		if($this->_component != 'DashBoard'){
			$this->_createMVC();
		}

		#check if user s logged in]
		global $registry;
		//$thisUser = unserialize($registry->get('session')->read('thisUser'));

		//var_dump($registry->get('session')->read('loggedIn'), $this->_component); die;
		if(!$registry->get('session')->read('loggedIn')  && $this->_component != 'Login'){
			$this->_controller = new LoginController(new LoginModel());
			$this->_method = 'execute';
			$this->_params['mthd'] = 'render';
		}

		//excute app
		//var_dump($this->_controller, $this->_method); die;
		call_user_func(array($this->_controller, $this->_method), $this->_params);
	}

	private function _createMVC(){
		$model = $this->_component . 'Model';
		#$view = $this->_component . 'View';

		#use only one view...since the view do nealy the same thing
		#if u ever need a dedicated view for a component, create it in the component cntrler and attach to the component's model
		$view = 'GeneralView';
		$ctrl = $this->_component . 'Controller';

		//create MVC
		$this->_model = new $model();
		$this->_controller = new $ctrl($this->_model);
		//$this->_view = new $view($this->_controller);

		$this->_view = new $view();

		//$this->_view->get('model')->set('view', $this->_view);

		//attach view observer to model object
		$this->_model->attach($this->_view);
		
	}



}
