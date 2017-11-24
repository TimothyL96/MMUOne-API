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
	$data = array('form_loginUsername' => $studentID, 'form_loginPassword' => $password);
	
	//	Create cookie file
	$cookie = tempnam("/cookie", "CURLCOOKIE");
	
	//	Connect to MMU PORTAL with cURL
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_POST, TRUE);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.93 Safari/537.36");
	curl_setopt($curl, CURLOPT_HEADER, FALSE);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
	curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);
	$result = curl_exec($curl);
	
	//	Get any cURL error
	curl_error($curl); 
	
	//	Check for id "headerWrapper" that will contain "Welcome, (Full Name)"
	//	Load the string to HTML DOM without stripping /r/n tags
	$htmlDOM->load($result, true, false);
	
	//	Find the desired input field
	$inputFullName = $htmlDOM->find('#headerWrapper');
	
	//	Get the token value
	echo $inputFullName[0]->plaintext;
	$fullName = trim($inputFullName->plaintext);	//filter away Welcome,
	
	//	Process the return value
	echo $fullName;
	
	//	URL of MMU Portal's Bulletion Boarx
	$url = "https://online.mmu.edu.my/bulletin.php";
	
	//	Close cUrl resource and free up system resources
	curl_close($curl);