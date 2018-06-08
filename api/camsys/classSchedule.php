<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 25/5/2018
	 * Time: 12:06 AM
	 */
	//	Get class schedule

	//	Headers
	require_once '../objects/header_get.php';

	//	Connection
	require_once '../config/connection.php';

	//	Users object
	require_once '../objects/users.php';

	//	Portal object
	require_once '../objects/camsys.php';

	//	Get Simple HTML DOM library
	require_once '../library/html_dom.php';

	//	Include cURL function: curl(url, postRequest, data, cookie)
	require_once '../objects/curl.php';

	//	Include Message Sender function
	require_once '../objects/messageSender.php';

	//	Instantiate users object and retrieve connection
	$db = new Database();
	$conn = $db->connect();

	//	Set connection for users table
	$users = new Users($conn);

	//	Set up Portal object
	$camsys = new camsys($conn);

	//	New Simple HTML DOM object
	$htmlDOM = new simple_html_dom();

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

	//	Set the student ID
	$users->student_id = $_GET['student_id'];

	//	Set cookie
	$cookie = "cookie/camsys_{$users->student_id}.cke";

	//	URL for getting class schedule
	$url = "https://cms.mmu.edu.my/psc/csprd/EMPLOYEE/HRMS/c/SA_LEARNER_SERVICES.SSR_SSENRL_SCHD_W.GBL?Page=SSR_SS_WEEK";

	//	It is a post request
	$postRequest = FALSE;

	//cURL
	$curl = NULL;

	//	Get class schedule for current trimester
	$curlResult = curl($curl, $url, $postRequest, $data = array(), $cookie);

	if (!$curlResult[0])
	{
		//	check account balance failed
		//	TODO ADD ERROR MESSAGE
		$this->error = 20601;

		return false;
	}

	//	Test
	print_r($curlResult[1]);

	//	Clear the HTML DOM memory leak
	$htmlDOM->clear();
