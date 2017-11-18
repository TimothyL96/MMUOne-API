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
	}
	else
	{
		//	Set error
		
		//	Echo JSON message
		
		//	Kill
		die();
	};
	
	//	Connect to MMU PORTAL with cURL