<?php
	//	***********************************
	//
	//				MMU PORTAL
	//
	//	***********************************

	$htmlDOM = "required";

	//	Users object
	require_once '../objects/users.php';

	require_once '../objects/portal_helper.php';

	//	Set connection for users table
	$users = new Users($conn);
	
	//	Set the student ID
	$users->student_id = $_GET['student_id'];

	//	Set cookie
	$cookie = "cookie/portal_{$users->student_id}.cke";

	//	Check if file exist
	if (!file_exists($cookie))
	{
		file_put_contents($cookie, "New file");
	}

	//	Retrieve user MMU IDM password to login to MMU Portal
	$users->readPasswordMMU();
	
	if (empty($users->password_mmu))
	{
		//	Failed to get user's MMU (IDM) password.
		//	Echo JSON message
		messageSender(0, "Failed to get user's MMU password", 99999);

		//	Kill
		die();
	}
	
	//	Set Login Credentials for MMU Portal
	$studentID = $users->student_id;
	$password = $users->password_mmu;
		
	//	Login to MMU Portal
	//	Check if login fails
	if (!login($studentID, $password, $cookie, $tokenClass))
	{
		//	Failed to login user to MMU Portal
		//	Echo JSON message
		messageSender(0, "Failed to login user to MMU Portal", 88888);

		//	Kill
		die();
	}

	//	Echo message
	messageSender(1, "Logged in");

	//	Login function with URL and cURL
	function login($studentID, $passwordMMU, $cookie, $tokenClass)
	{
		//	Login user
		//	Session ends when browser ends

		//	URL of MMU Portal login
		$url = "https://online.mmu.edu.my/index.php";

		//	Data for Login POST
		$data = array('form_loginUsername' => $studentID, 'form_loginPassword' => $passwordMMU);

		//	It is a POST request
		$postRequest = TRUE;

		$portalData = portalInclude(array("tab"), array($url, $postRequest, 987665, $data), $tokenClass);

		//	Check return data
		if (!$portalData)
		{
			//	If false, means cURL failed
			messageSender(0, "Portal Data return error!", 11111);
			die();
		}

		if (strpos($portalData, "WELCOME TO MMU ONLINE PORTAL!") !== FALSE)
		{
			return FALSE;
		}

		return TRUE;
	}
