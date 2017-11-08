<?php
	//	Headers
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: POST");
	header("Access-Control-Max-Age: 3600");
	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
	
	//	Require connection and users functions
	require_once '../config/connection.php';
	require_once '../objects/users.php';
	
	//	New users objects
	$db = new Database();
	$conn = $db->connect();
	
	//	Setup connection for users class
	$users = new Users($conn);
	
	//	Get posted data
	$data = json_decode(file_get_contents("php://input"));
	
	//	Pass values to Users class variables
	$users->student_id = $data->student_id;
	$users->password_mmuone = $data->password_mmuone;
	
	//	Log the user in
	if ($users->loginUser())
	{
		//	Succeeded
		echo "{";
		echo "\"status\": \"succeed\",";
		echo "\"messsage\": \"User successfully logged in\"";
		echo "}";
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
	
		echo "{";
		echo "\"status\": \"failed\",";
		echo "\"code\": \"{$errorCode}\",";
		echo "\"message\": \"{$errorText}\"";
		echo "}";
	}