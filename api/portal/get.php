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
	
	//	Instantiate users object and retrieve connection
	$db = new Database();
	$conn = $db->connect();
	
	//	Set student ID of user to be read
	if (isset($_GET['student_id']))
	{
		//	Retrieve user MMU IDM password to login to MMU Portal
		
		//	Login Credentials for MMU Portal
		$studentID = $_GET['student_id'];
		$password = "Tasem0707@";
	}
	else
	{
		//	Set error
		
		//	Echo JSON message
		
		//	Kill
		die("No student ID specified");
	};
	
	//	URL of MMU Portal
	$url = "https://online.mmu.edu.my/index.php";
	
	//	Data for Login POST
	$data = array('form_loginUsername' => $studentID, 'login-password' => $password);
	
	//	Connect to MMU PORTAL with cURL
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_POST, TRUE);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.93 Safari/537.36");
	curl_setopt($curl, CURLOPT_HEADER, FALSE);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
	$result = curl_exec($curl);
	
	//	cURL error
	echo 'Error: ' . curl_error($curl);
	
	//	Process the return value
	echo $result;