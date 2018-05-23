<?php
	//	***********************************
	//
	//				MMU PORTAL
	//
	//	***********************************
	
	//	Headers
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: access");
	header("Access-Control-Allow-Methods: GET");
	header("Access-Control-Allow-Credentials: true");
	header("Content-Type: application/json; charset=UTF-8");
	
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

	//	Check if file exist
	if (!file_exists($cookie))
	{
		file_put_contents($cookie, "New file");
	}

	//	Retrieve user MMU IDM password to login to MMU Portal
	$users->readPasswordMMU();

	if (empty($users->password_mmu))
	{
		//	Failed to get user's MMU (IDM) password.
		//	TODO Set error

		//	Echo JSON message

		//	Kill
		die("Failed to get user's MMU password");
	}

	//	Set Login Credentials for MMU Portal
	$studentID = $_GET['student_id'];
	$password = $users->password_mmu;

	////////////	OLD
	//	URL of MMU Portal
	$url = "https://cms.mmu.edu.my/psp/csprd/?cmd=login&languageCd=ENG";
	
	//	Data for Login POST
	$data = array('userid' => $studentID, 'pwd' => $password);

	//	It is a post request
	$postRequest = true;
	//cURL
	$curl = NULL;

	//	Connect to CamSYS with cURL to get cookie
	$curlResult = curl($curl, $url, $postRequest, $data, $cookie);

	if (!$curlResult[0])
	{
		//	log in failed
		//	TODO ADD ERROR MESSAGE
		$this->error = 20601;

		return false;
	}

	//	Login to CamSYS with cURL
	$curlResult = curl($curl, $url, $postRequest, $data, $cookie);

	if (!$curlResult[0])
	{
		//	log in failed
		//	TODO ADD ERROR MESSAGE
		$this->error = 20601;

		return false;
	}

	//	TODO check if log in succeeded
	print_r($curlResult[1]);
