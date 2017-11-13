<?php
/*
* Interface that all Uploader Classes must Implement
*
*/

defined('ACCESS') || Error::exitApp();

interface UploadHandler{

	public function UploadFile();	
	
}