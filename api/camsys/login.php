<?php
	//	***********************************
	//
	//				Camsys
	//
	//	***********************************

	//	Require camsys helper
	require_once '../objects/camsys_helper.php';

	//	Users object
	require_once '../objects/users.php';
	
	//	Set connection for users table
	$users = new Users($conn);

	//	Set the student ID
	$headers = apache_request_headers();
	if ($tokenClass->getStudentID($headers['Authorization']))
	{
		$users->student_id = $tokenClass->student_id;
	}
	$users->student_id = "1142700462";
	//	Set cookie
	$cookie = "cookie/camsys_{$users->student_id}.cke";

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
		//	TODO Set error

		//	Echo JSON message

		//	Kill
		messageSender(0, "Failed to get user's MMU password", 99999);
	}

	//	Set Login Credentials for MMU Portal
	$studentID = $users->student_id;
	$password = $users->password_mmu;

	//	URL of MMU Portal
	$url = "https://cms.mmu.edu.my/psp/csprd/?cmd=login&languageCd=ENG";
	
	//	Data for Login POST
	$data = array('userid' => $studentID, 'pwd' => $password);

	//	It is a post request
	$postRequest = true;

	$camsysData = camsysInclude(array($url, $postRequest, 123459, $data), $tokenClass);

	$camsysData = camsysInclude(array($url, $postRequest, 123459, $data), $tokenClass, 1);

	if (strpos($camsysData, "Sign out") === FALSE)
	{
		messageSender(0, "Login failed", 666999);
	}
	else
	{
		messageSender(1, "login succeeded cmsys");
	}
