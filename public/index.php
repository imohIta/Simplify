<?php
/**
 * Front-end application entry point
 */

define("ACCESS", true);
define ("PATH", realpath(__DIR__ . '/../'));

# set default time zone
date_default_timezone_set('Africa/Lagos');


//include files
require_once PATH . '/core/libs/error.class.php';
require_once PATH . '/core/libs/funiFactory.ini.php';
require_once PATH . '/core/libs/bootstrap.class.php';
require_once PATH . '/core/libs/session.class.php';

//var_dump(password_hash('Admin123', PASSWORD_DEFAULT));
//Create Session Object
global $session;
$session = new core\libs\Session(array(
								'name' => 'KelvicSuitesHMS',
								//'domain' => 'localhost',
								'httponly' => true
							));
$session->start();

$app = Application::getInstance($registry->get('autoLoader'), $session, $registry->get('router'));

//register session object
$registry->set('session', $session);


//include page where global classes have been registered
require_once PATH . '/application/libs/appFactory.ini.php';

require_once PATH . '/application/libs/functions.php';

	//var_dump(password_hash('12345', PASSWORD_DEFAULT));die;



//Execute App
$app->boot();


#baseUri : http://localhost:8888/kelvic_hms/public
#admin pwd : iamlegend1121



/* *************************************************************
						TEST HERE
****************************************************************/
//var_dump($registry->get('autoLoader'));
//$uploader = new FuniUploader($registry->get('config')->get('basePath') . '/public/imgs/20140501101741.jpg');
//var_dump($uploader); die;
//$registry->get('router')->get('view')->get('model')->attach($registry->get('router')->get('view'));
//var_dump($registry->get('router')->get('model'));
//echo $registry->get('minifier')->minify(file_get_contents(PATH . '/application/libs/appFactory.ini.php')); die;
//var_dump($registry->get('includer'));
//$rc = new ReflectionClass('Includer');

//var_dump(class_exists($rc->hasMethod('render')));
//echo $registry->get('config')->get('basePath');

//var_dump( $registry->get('db')->get('connections')[ $registry->get('db')->get('activeConnections') ]);

/*$options = array(
				 'table' => 'users',
				 'query' => 'CALL sp_AddUser',
				 'params' => array(
				 				  'name' => 'OEdet2',
								  'password' => 'mupphy',
								  'username' => '@iffy',
								  'access_level' => 1
								)
				);
$delete  = array(
				'table' => 'users',
				'query' => 'CALL sp_DeleteUser',
				'condition' => "`name` = 'OEdet2'",
				'limit' => 1
				);*/
//$registry->get('db')->insert($options, true);
//$registry->get('db')->delete($delete, false);
