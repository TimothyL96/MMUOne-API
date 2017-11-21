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
	
	//	Get Simple HTML DOM library
	require_once '../library/html_dom.php';
	
	//	New Simple HTML DOM object
	$htmlDOM = new simple_html_dom();
	
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
		$token = "";
	}
	else
	{
		//	Set error
		
		//	Echo JSON message
		
		//	Kill
		die("No student ID specified");
	};
	
	//	URL of MMU Portal
	$url = "https://mmls.mmu.edu.my/";
	
	//	Data to get Login Cookies
	$data = array('_token' => $token,'stud_id' => $studentID, 'stud_pswrd' => $password);
	
	//	Create cookie file
	$cookie = tempnam("/cookie", "CURLCOOKIE");
	
	//	Connect to MMU PORTAL with cURL
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_POST, FALSE);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36");
	curl_setopt($curl, CURLOPT_HEADER, FALSE);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);
	$result = curl_exec($curl);
	
	//	Get the token to login
	//$htmlDOMObject = str_get_html($result);
	$htmlDOM->load($result, true, false);
	$ret = $htmlDOM->find('input[name=_token]');
	echo $ret[0]->value;
	foreach ($ret as $rete)
	{
		//echo $rete->value . "<br/>";
	}
	
	die("132");
	
	//	Set the URL for POST login
	$url = "https://mmls.mmu.edu.my/checklogin";
	
	//	Connect to MMU PORTAL with cURL
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_POST, TRUE);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36");
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