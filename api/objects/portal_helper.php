<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 08/6/2018
	 * Time: 11:40 PM
	 */

	//	Headers
	require_once '../objects/header_get.php';

	//	Connection
	require_once '../config/connection.php';

	//	Include Message Sender function
	require_once '../objects/messageSender.php';

	//	Include token validation function
	require_once '../objects/tokenManagement.php';

	//	Portal object
	require_once '../objects/portal.php';

	//	Check if exist to exclude HTML DOM
	if ($htmlDOM == "required")
	{
		//	Get Simple HTML DOM library
		require_once '../library/html_dom.php';

		//	New Simple HTML DOM object
		$htmlDOM = new simple_html_dom();
	}

	//	Instantiate users object and retrieve connection
	$db = new Database();
	$conn = $db->connect();

	//	Set up Portal object
	$portal = new Portal($conn);

	$tab = NULL;

	//	Remaining body:
	//	Accept parameter: An array that can have value of:
	//	- cookie
	//	- tab
	//
	//	Second parameter: An array fill with necessary data for CuRL
	//	- URL
	//	- postRequest: TRUE or FALSE
	//	- error number
	//	- data in array

	function portalInclude($toExclude = array(), $curlData = array())
	{
		//	Include cURL function: curl(url, postRequest, data, cookie)
		require_once '../objects/curl.php';

		//	Get all HTTP headers
		$headers = apache_request_headers();

		//	Check if authorization header set
		if (!isset($headers['Authorization']))
		{
			//	Invalid request - echo error in JSON and die
			messageSender(0, "No token received", 123456);
			die();
		}

		//	Retrieve token
		$token = $headers['Authorization'];

		//	Peform token validation - Retrieve student ID from related token
		$student_id = tokenValidation($token);
		if (!$student_id)
		{
			messageSender(0, "Invalid token received", 123457);
			die();
		}
		tokenGeneration($student_id);

		//	Check if exist to exclude tab
		if (!in_array("tab", $toExclude))
		{
			//	Get $_GET data
			//	Check if tab provided
			if (empty($_GET['tab']))
			{
				//	TODO Set error

				//	Echo JSON message
				messageSender(0, "No tab provided", 123457);

				//	Kill
				die();
			}
			$tab = $_GET['tab'];
		}

		//	Check if exist to exclude cookie
		$cookie = NULL;
		if (!in_array("cookie", $toExclude))
		{
			//	Set cookie
			$cookie = "cookie/portal_{$student_id}.cke";
		}

		if (empty($curlData))
		{
			return TRUE;
		}

		//cURL start
		$curl = NULL;

		$curlResult = curl($curl, $curlData[0], $curlData[1], $curlData[3] = array(), $cookie);

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
