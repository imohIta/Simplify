<?php
use core\libs;

defined("ACCESS") || die('');

require_once 'base/funiObject.class.php';
require_once realpath(__DIR__ . '/../') . '/configs/config.class.php';
require_once 'autoloader.class.php';
require_once 'session.class.php';
require_once 'router.class.php';
require_once 'registry.class.php';
require_once 'uri.class.php';


/**
* Create all Core Objects that the Framework require to Boot
* Should Always be require in the index page before requiring appFactory and before Booting App
*/

$env = stripos($_SERVER['HTTP_HOST'], 'localhost') === false
		? 'production' : 'development';


//create config object
$configArray = ($env == 'development')
				? array(
								  'basePath' => PATH,
								  'baseUri' => 'http://' . $_SERVER['HTTP_HOST'] . '/kelvic_hms/public',
								  'appTitle' => 'Kelvic Suites HMS',
								  'ds' => '/',
								  'dbHost' => 'localhost',
								  'dbName' => 'kelvic_hms',
								  'dbPwd' => 'root',
								  'dbUser' => 'root' )
				: array(
					'basePath' => PATH,
					'baseUri' => 'http://simplify.ozbcommunicationz.com/public',
					'appTitle' => 'Kelvic Suites HMS',
					'ds' => '/',
					'dbHost' => 'localhost',
					'dbName' => 'kelvic_hms',
					'dbPwd' => 'root',
					'dbUser' => 'root'
				);

$config = new Config($configArray);



//create Autoloader Object
$autoloader = new Autoloader(array('application','core', 'installer'), $config);

/*//Create Session Object
$session = new core\libs\Session(array(
								'name' => 'KelvicSuitesHMs',
								'domain' => 'localhost',
								'httponly' => true
							));*/
//create Router Object
#when using windows...u can omit the second param cos it is defaulted
$router = new Router($_SERVER['REQUEST_URI'], '/kelvic_hms/public/');

$uri = new Uri($config);


/**
* Register all FrameWork Boot Class
* So they become accessible throught the App
*/

$registry = Registry::getInstance();

$registry->set('config', $config);
$registry->set('autoLoader', $autoloader);
//$registry->set('session', $session);
$registry->set('router', $router);
$registry->set('uri', $uri);
