<?php

use application\libs\Database as Database;
use application\libs\LoggerDatabase;
use application\libs\Logger;

use application\libs\TransactionDatabase;
use application\libs\GuestDatabase;
use application\libs\BillDatabase;
use application\libs\PaymentDatabase;
use application\libs\RoomDatabase;
use application\libs\ItemDatabase;
use application\libs\MenuDatabase;
use application\libs\ImpressDatabase;





defined('ACCESS') || Error::exitApp();

/**************************
	FRAMEWORK CLASESS
***************************/

$minifier = new Minifier;

//pass all Database Options here
$database = new Database(array('dbName' => $registry->get('config')->get('dbName'),
								 'user' => $registry->get('config')->get('dbUser'),
								 'password' => $registry->get('config')->get('dbPwd')));
								
								

$loggerDb = new LoggerDatabase(array());


$transDb = new TransactionDatabase(array());
$guestDb = new GuestDatabase(array());
$billsDb = new BillDatabase(array());
$payDb = new PaymentDatabase(array());
$roomDb = new RoomDatabase(array());
$itemDb = new ItemDatabase(array());
$menuDb = new MenuDatabase(array());
$impressDb = new ImpressDatabase(array());

$db = new Database(array());

//create included options
$includer = new Includer($registry->get('config'), array(
 								'tmplPath' => 'application/components/views/parts',
								'header' => 'header',
								'sidebar' => 'sidebar',
								'footer' => 'footer'
							  ));

//create Sanitizer object
$form = new Sanitizer();

//create Logger
$logger = new logger($loggerDb);

//authenticator object
$authenticator = new authenticator($database); #change database object to the own that will actually make the calls to user table

$registry->set('minifier', $minifier);
$registry->set('db', $database);
$registry->set('includer', $includer);
$registry->set('form', $form);
$registry->set('authenticator', $authenticator);
$registry->set('logger', $logger);

$registry->set('guestDb', $guestDb);
$registry->set('transDb', $transDb);
$registry->set('billsDb', $billsDb);
$registry->set('payDb', $payDb);
$registry->set('roomDb', $roomDb);
$registry->set('itemDb', $itemDb);
$registry->set('menuDb', $menuDb);
$registry->set('impressDb', $impressDb);







/**************************
	APPLICATION CLASESS
***************************/