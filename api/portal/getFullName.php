<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 29/3/2018
	 * Time: 5:53 PM
	 */

	//	Headers
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: access");
	header("Access-Control-Allow-Methods: GET");
	header("Access-Control-Allow-Credentials: true");
	header("Content-Type: application/json; charset=UTF-8");
	//	TODO TOKEN AND COOKIE AUTHORIZATION
	//	TODO ADD COMMENTS

	//	Connection
	require_once '../config/connection.php';

	//	Users object
	require_once '../objects/users.php';

	//	Portal object
	require_once '../objects/portal.php';

	//	Get Simple HTML DOM library
	require_once '../library/html_dom.php';

	//	Include cURL function: curl(url, postRequest, data, cookie)
	require_once '../objects/curl.php';

	//	New Simple HTML DOM object
	$htmlDOM = new simple_html_dom();

	//	Instantiate users object and retrieve connection
	$db = new Database();
	$conn = $db->connect();

	//	Set up Portal object
	$portal = new Portal($conn);

	//	Set error
	$error = 00000;

	//	Check if Student ID provided
	if (empty($_GET['student_id']))
	{
		//	TODO Set error

		//	Echo JSON message

		//	Kill
		die("No student ID specified");
	}
	$student_id = $_GET['student_id'];

	//	Check if cookie provided
	if (empty($_GET['cookie']))
	{
		//	TODO Set error

		//	Echo JSON message

		//	Kill
		die("No student ID specified");
	}
	$cookie = $_GET['cookie'];

	//	URL of MMU Portal
	$url = "https://online.mmu.edu.my/index.php";

	//	It is a GET request
	$postRequest = FALSE;

	//cURL
	$curl = NULL;

	$curlResult = curl($curl, $url, $postRequest, $data, $cookie);

	if (!$curlResult[0])
	{
		//	get name failed
		//	TODO ADD ERROR MESSAGE
		$this->error = 20601;

		return false;
	}

	//	Close cURL resource and free up system resources
	if (!is_null($curl))
	{
		curl_close($curl);
	}

	//	Check for id "headerWrapper" that will contain "Welcome, (Full Name)"
	//	Load the string to HTML DOM without stripping /r/n tags
	$htmlDOM->load($curlResult[1], true, false);

	//	Find the desired input field
	$inputFullName = $htmlDOM->find('#headerWrapper .floatL');

	//	Get the full name by filtering text at the front and back
	$fullName = trim(substr(trim($inputFullName[0]->plaintext), 8, strripos($inputFullName[0]->plaintext, "(") - 8));

	//	Echo the full name
	messageSender(1, $fullName);