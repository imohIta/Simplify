<?PHP
include_once('OziomaApiImpl.php');

//instantiate objection of the ozioma api implementation
$ozioma_api = new OziomaApiImpl();

//sending message
$ozioma_api->set_message("Your message here");
$ozioma_api->set_recipient('2348188984391');//separate numbers with commas and include zip code in every number
$ozioma_api->set_sender('Ozioma');

//if you want to schedule your message
$ozioma_api->set_schedule_date('2012-11-12 10:50');
//$ozioma_api->schedule();//for scheduling of message

$ozioma_api->send();
if($ozioma_api->get_status() == 'OK')
{
	//successful
	//do something with the message
}

//checking balance
//$ozioma_api->check_balance();
if($ozioma_api->get_status() == 'OK')
{
	$bal = $ozioma_api->get_balance();
}

//$ozioma_api->fetch_message('105');
if($ozioma_api->get_status() == 'OK')
{
	//successful
	//do something with the message
}
