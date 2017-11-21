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
	
	//	Instantiate users object and retrieve connection
	$db = new Database();
	$conn = $db->connect();
	
	//	Set connection for users table
	$users = new Users($conn);
	
	//	Check if Student ID provided
	if (!empty($_GET['student_id']))
	{
		//	Retrieve user MMU IDM password to login to MMU Portal
		
		//	Set Login Credentials for MMU Portal
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
	$url = "https://mmls.mmu.edu.my/checklogin";
	
	//	Data for Login POST
	$data = array('stud_id' => $studentID, 'stud_pswrd' => $password);
	
	//	Create cookie file
	$cookie = tempnam("/cookie", "CURLCOOKIE");
	
	//	Connect to MMU PORTAL with cURL
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_POST, TRUE);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/534.24 (KHTML, like Gecko) Ubuntu/10.04 Chromium/11.0.696.0 Chrome/11.0.696.0 Safari/534.24");
	curl_setopt($curl, CURLOPT_HEADER, FALSE);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
	curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);
	$result = curl_exec($curl);
	
	//	Get any cURL error
	curl_error($curl);
	$status = curl_getinfo($curl, CURLINFO_COOKIELIST); 
	
	//	Process the return value
	echo $result;
	
	//	Close cUrl resource and free up system resources
	curl_close($curl);