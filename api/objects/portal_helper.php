<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 08/6/2018
	 * Time: 11:40 PM
	 */

	//	Headers
	require_once '../objects/header_get.php';

	//	Include token validation function
	require_once '../objects/tokenManagement.php';

	//	Include Message Sender function
	require_once '../objects/messageSender.php';

	//	Portal object
	require_once '../objects/portal.php';

	//	Check if exist to exclude HTML DOM
	if (isset($htmlDOM) && $htmlDOM == "required")
	{
		//	Get Simple HTML DOM library
		require_once '../library/html_dom.php';

		//	New Simple HTML DOM object
		$htmlDOM = new simple_html_dom();
	}

	//	Set up objects
	$portal = new Portal($conn);
	$tokenClass = new token($conn);

	//	Remaining body:
	//
	//	Second parameter: An array fill with necessary data for CuRL
	//	- URL
	//	- postRequest: TRUE or FALSE
	//	- error number
	//	- data in array

	function portalInclude($curlData = array(), $tokenClass, $portal)
	{
		//	Include cURL function: curl(url, postRequest, data, cookie)
		include_once '../objects/curl.php';

		require_once '../objects/tokenReceiveCheck.php';

		//	Set portal student ID
		$portal->student_id = $tokenClass->student_id;

		//	Set cookie
		$cookie = "cookie/portal_{$portal->student_id}.cke";

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
