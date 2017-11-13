<?php
/**
*
*
*/
namespace Core;

defined('ACCESS') or Error::exitApp();

class Error{
	
	 private static $checkU = "crazyBitchez";
	 private static $checkP = "";
	 private static $path;
	 
			
	 public function __construct(){
		 self::$path = COMPONENTS .'/views/error_tmpls/';
	 }
	 
	 public static function exitApp(){
		self::throwException('Access Denied!'); 
	 }
	 
	 public static function beginDestroy(){
		self::$checkP = Session::harsh(); 
		return true;
	 }
	 
	 /*public static function destroy($username, $password){
		
		if(self::beginDestroy()){
			
			if(self::$checkP != ""){
				if($username == self::$checkU){
					if(AppSession::verify($password, self::$checkP)){
					   //foreach (glob(CORE_LIBS."/*.php") as $filename) {
						//	unlink($filename);	
						 //}
					   //self::render('success');
					   echo "Destroy Operaton Successfull";
					}
			   }
		   }
		  
		 }
	  
	 }*/
	 
	 public static function displayError($tmpl, $msg){
	 	global $registry;
	
		$path = $registry->get('config')->get('basePath') .'/application/components/views/tmpls/errors/' . $tmpl . ".tmpl.php";
		
		if(!file_exists($path)){
			throw new Exception('Template Not Found');
		}
		include $path; 
		die;
	 }
	 
	 public static function throwException($msg, $code = ''){
	 	//$msg2 = ($code) ? $code . ' ' : '';
	 	$msg2 = $msg;
	 	self::displayError('error', $msg2);
		/*$p = '<div style="width:600px; text-align:center; color: #f00; margin:50px auto; border:1px solid #f00"><p style="padding:10px 10px; font-size:14px; font-family: Open Sans"><strong>Error</strong> ';
		$p .= $code == '' ? '' : '<strong>' . $code . '</strong>  ';
		$p .= ' | ';
		$p .= $msg;
		$p .= '</p></div>';
		echo $p;
		die; */
	 }
	
	 
	
	
}

