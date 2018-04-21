<?php
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
	
	//	Set student ID of user to be read
	if (!empty($_GET['student_id']))
	{
		$users->student_id = $_GET['student_id'];
	}
	else
	{
		$users->message['status'] = "failed";
		$users->message['code'] = $users->error;
		$users->message['message'] = $users->getErrorText(10600);
		
		//	Echo JSON message
		$users->echoMessage();
		
		//	Kill
		die("No student ID specified");
	};
	
	//	Read one
	$users->readOneByStudentID();
	
	//	Create array
	$users_array = array(
		"full_name" => $users->full_name,
		"student_id" => $users->student_id,
		"email" => $users->email,
		"password_mmuone" => $users->password_mmuone,
		"password_mmu" => $users->password_mmu,
		"faculty" => $users->faculty,
		"campus" => $users->campus,
		"profile_pic" => $users->profile_pic,
		"last_login" => $users->last_login,
		"date_registered" => $users->date_registered
		);
	
	//	Echo in JSON format
	$users->message['status'] = "succeed";
	$users->message['message'] = json_encode($users_array);