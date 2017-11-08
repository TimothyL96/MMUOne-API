<?php
	//	Headers
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: POST");
	header("Access-Control-Max-Age: 3600");
	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
	
	//	Connection
	require_once '../config/connection.php';
	
	//	Users object
	require_once '../objects/users.php';
	
	//	Instantiate users object and retrieve connection
	$db = new Database();
	$conn = $db->connect();
	
	//	Set connection for users table
	$users = new Users($conn);
	
	//	Get posted data
	$data = json_decode(file_get_contents("php://input"));
	
	//	Set user's input values
	$users->full_name = $data->full_name;
	$users->email = $data->email;
	$users->student_id = $data->student_id;
	$users->password_mmuone = $data->password_mmuone;
	$users->date_registered = date('Y-m-d H:i:s');
	
	//	Create the product
	if ($users->loginUser())
	{
		echo "{";
		echo "\"status\": \"succeed\",";
		echo "\"messsage\": \"User successfully registered\"";
		echo "}";
	}
	else
	{
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