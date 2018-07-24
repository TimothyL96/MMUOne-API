<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 24/7/2018
	 * Time: 10:24 PM
	 */

	//	Headers
	require_once '../objects/header_get.php';

	//	MMLS object
	require_once '../objects/mmls.php';

	//	HTML DOM Library
	require_once '../library/html_dom.php';

	//	Token management methods
	require_once '../objects/tokenManagement.php';

	//	messageSender() function
	require_once '../objects/messageSender.php';

	//	Set up objects
	$mmls = new mmls($conn);
	$tokenClass = new token($conn);

	//	mmlsInclude: To act as a surface for cURL call
	//	Accepted parameters:
	//	array(), tokenClass, $toLogin
	//
	function mmlsInclude($curlData = array(), $tokenClass, $toLogin = 0)
	{
		//	Include cURL function: curl(url, postRequest, data, cookie)
		include_once '../objects/curl.php';

		//require '../objects/tokenReceiveCheck.php';

		$student_id = $tokenClass->student_id;
		$student_id = "1142700462";

		//	Set cookie
		$cookie = "cookie/mmls_{$student_id}.cke";

		if (empty($curlData))
		{
			return TRUE;
		}

		//cURL start
		$curl = NULL;

		$curlResult = curl($curl, $curlData[0], $curlData[1], $curlData[3], $cookie);

		//	If result invalid
		if (!$curlResult[0])
		{
			//	cURL failed
			//	TODO ADD ERROR MESSAGE
			//$error = $curlData[2];
			messageSender(0, "mmls curlResult[0] is not true", 7890987);

			return false;
		}

		//	Close cURL resource and free up system resources
		if (!is_null($curl))
		{
			curl_close($curl);
		}

		//	Return successful message
		return $curlResult[1];
	}