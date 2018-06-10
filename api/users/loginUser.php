<?php
	//	Headers
	require_once '../objects/header_post.php';

	//	Require connection and users functions
	//require_once '../config/connection.php';
	require_once '../objects/users.php';
	require_once '../objects/tokenManagement.php';
	require_once '../objects/messageSender.php';
	
	//	New users objects
	//$db = new Database();
	//$conn = $db->connect();

	$token = new token($conn);

	//	Setup connection for users class
	$users = new Users($conn);
	
	//	Get posted data
	$data = json_decode(file_get_contents("php://input"));
	
	//	Pass values to Users class variables
	$users->student_id = $data->student_id;
	$users->password_mmuone = $data->password_mmuone;

	//	Status: 1 or 0
	$status = 0;
	
	//	Log the user in
	if ($users->loginUser())
	{
		//	Succeeded
		//	Set the student ID to token
		$token->student_id = $users->student_id;

		//	Check first time user
		checkUserExist($users->student_id, $token);

		//	Update user current device MAC address
		macAddrUpdate($users->student_id, $data->macaddr, $token);

		//	Generate access token
		tokenGeneration($users->student_id, $token);

		//	Set values for message array
		$status = "1";
		$users->message['code'] = 0;
		$users->message['message'] = "User successfully logged in";
	}
	else
	{
		//	Failed
		//	Set error
		if (!empty($users->error))
		{
			$errorText = $users->getErrorText();
			$errorCode = $users->error;
		}
	
		//	Set values for message array
		$status = "0";
		$users->message['code'] = $errorCode;
		$users->message['message'] = $errorText;
	}

	//	Echo JSON message
	//$users->echoMessage();
	messageSender($status, $users->message);