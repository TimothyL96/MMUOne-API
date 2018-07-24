<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 11/6/2018
	 * Time: 4:59 PM
	 */

	//	Headers
	require_once '../objects/header_get.php';

	//	Portal object
	require_once '../objects/camsys.php';

	//	Get Simple HTML DOM library
	require_once '../library/html_dom.php';

	//	Token management methods
	require_once '../objects/tokenManagement.php';

	//	Include Message Sender function
	require_once '../objects/messageSender.php';

	//	Set up objects
	$camsys = new camsys($conn);
	$tokenClass = new token($conn);

	//	Include specific code and cURL call
	//	Accept parameter:
	//	toLogin: boolean. True to get token from tokenStore instead of apache header
	//
	//
	function camsysInclude($curlData = array(), $tokenClass, $toLogin = 0)
	{
		//	Include cURL function: curl(url, postRequest, data, cookie)
		include_once '../objects/curl.php';

		require '../objects/tokenReceiveCheck.php';

		$student_id = $tokenClass->student_id;
		//$student_id = "1142700462";
		//	Set cookie
		$cookie = "cookie/camsys_{$student_id}.cke";

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
			messageSender(0, "curlResult[0] is not true", 95123123);

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