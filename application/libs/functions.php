<?php

defined('ACCESS') or Error::exitApp();

#define global variables
global $today;



$today = date('Y-m-d');


function dateToString($date){
	return date('jS F Y', strtotime($date));
}

function today(){
	return date('Y-m-d');
}

function yesterday(){
	return date('Y-m-d', strtotime("-1 days", strtotime(date('Y-m-d'))));
}

function oneDayAgo($date){
	return date('Y-m-d', strtotime("-1 days", strtotime($date)));
}

function tomorrow(){
	return date('Y-m-d', strtotime("+1 days", strtotime(date('Y-m-d'))));
}

function now()
{
	# code...
	return time();
}

function autoBillTime(){
	return strtotime("tomorrow 1pm");
}

function timeToString($time){
	echo date('g:i:s a', $time);
}

function generateTransId(){
	$characters = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
	$generated_id = "";
	for( $i=1; $i<=2; $i++){
		$number = rand(0, 25);
		$generated_id .= $characters[$number];
	}
	$generated_id .= '-';

	for( $i=1; $i<=6; $i++){
		$generated_id .= rand(0, 9);;
	}
	return $generated_id;
}

function fetchGuestNameByPhone($guestPhone){
	global $registry;
	return Guest::fetchNameByPhone($guestPhone);
}

function amtToInt($amt){
	$amt = rtrim($amt);
	$amt = str_replace(',', '', $amt);
	$amt = filter_var($amt, FILTER_SANITIZE_NUMBER_INT);
	return $amt;
}

function fetchRoomIdByNo($roomNo){
	global $registry;
	$data = Room::fetchByNo($roomNo);

	return !empty($data) ? $data->id : null;
}

function getTblByPriv($priv){
	global $registry;

	switch ($priv) {
		case 8:
			return 'pool_barStk';
			break;
		case 9:
			return 'main_barStk';
			break;
		case 10:
			return 'resturantStk';
			break;
		case 11:
			return 'returant_drinksStk';
			break;
		case 12:
			return 'kitchenStk';
			break;
		case 13:
			return 'store';
			break;
		case 15:
			return 'house_keepingStk';
			break;

		default:
			return '';
			break;
	}
}

function getMonthLastDate($month){
	switch ($month) {
		case 4: case 6: case 9: case 11:
			return 30;
			break;
		case 2:
			return date('L') == 1 ? 29 : 28; # returns 29 if year os a leap year
		    break;
		default:
			return 31;
			break;
	}
}


function getNotType($getNotType){
	global $registry;
	return $registry->get('db')->getNotType($getNotType);

}

function setShiftTimes(){
	global $registry;
	$db = $registry->get('db');
	$session = $registry->get('session');

	$shiftTime = $db->fetchShiftTimes(today());

	# set shift time for today if shift time for today was not found in the database and the current time is more
	# that 8am
	if(is_null($shiftTime) || false === $shiftTime && time() >= strtotime("today 8am")){
			$db->insertShiftTimes(array(
				'beginTime' =>strtotime("today 8am"),
				'endTime' => strtotime("tomorrow 8am")
				));
			$session->write('shiftBeginTime', strtotime("today 8am"));
			$session->write('shiftEndTime', strtotime("tomorrow 8am"));
	}else{

		if(time() > $shiftTime->endTime){
			$db->insertShiftTimes(array(
				'beginTime' =>strtotime("today 8am"),
				'endTime' => strtotime("tomorrow 8am")
				));
			$session->write('shiftBeginTime', strtotime("today 8am"));
			$session->write('shiftEndTime', strtotime("tomorrow 8am"));
		}else{
			$session->write('shiftBeginTime', $shiftTime->beginTime);
			$session->write('shiftEndTime', $shiftTime->endTime);
		}
	}
}




