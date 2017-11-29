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
	require_once '../objects/portal.php';
	
	//	Get Simple HTML DOM library
	require_once '../library/html_dom.php';
	
	//	New Simple HTML DOM object
	$htmlDOM = new simple_html_dom();
	
	//	Instantiate users object and retrieve connection
	$db = new Database();
	$conn = $db->connect();
	
	//	Set connection for users table
	$users = new Users($conn);
	
	//	Set up Portal object
	$portal = new Portal($htmlDOM);
	
	//	Check if Student ID provided
	if (empty($_GET['student_id']))
	{
		//	Set error
		
		//	Echo JSON message
		
		//	Kill
		die("No student ID specified");	
	}
	
	//	Set the student ID
	$users->student_id = $_GET['student_id'];
	
	//	Retrieve user MMU IDM password to login to MMU Portal
	$users->readPasswordMMU();
	
	if (empty($users->password_mmu))
	{
		//	Failed to get user's MMU (IDM) password.
		//	Set error
		
		//	Echo JSON message
		
		//	Kill
		die("Failed to get user's MMU password");
	}
	
	//	Set Login Credentials for MMU Portal
	$studentID = $_GET['student_id'];
	$password = $users->password_mmu;
		
	//	Login to MMU Portal
	//	Check if false
	if (!$portal->login($studentID, $password))
	{
		//	Failed to login user to MMU Portal
		//	Set error (error code 20601)
		
		//	Echo JSON message
		
		//	Kill
		die("Failed to login user to MMU Portal");
	}
	
	//	Get bulletin
	//	Check if false
	//	$bulletin will be an array with 3 members that are array as well
	$tab = 1;
	if (!$bulletin = $portal->getBulletin($tab))
	{
		//	Failed to get bulletins from MMU Portal
		//	Set error (error code 20602)
		
		//	Echo JSON message
		
		//	Kill
		die("Failed to get bulletins from MMU Portal");
	}
	
	foreach($bulletin as $news)
	{
		//echo $key . ": " . html_entity_decode($news) . '\n';	
	}
	
	//	serialize object to be reused later
	//print_r(serialize($portal));