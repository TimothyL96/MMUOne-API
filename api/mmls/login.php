<?php
	//	***********************************
	//
	//				MMU PORTAL
	//
	//	***********************************

	//	Require camsys helper
	require_once '../objects/mmls_helper.php';
	
	//	Users object
	require_once '../objects/users.php';
	
	//	Set connection for users table
	$users = new Users($conn);

	//	New Simple HTML DOM object
	$htmlDOM = new simple_html_dom();

	//	Set the student ID
	$headers = apache_request_headers();
	//if ($tokenClass->getStudentID($headers['Authorization']))
	{
		$users->student_id = $tokenClass->student_id;
	}
	$users->student_id = "1142700462";

	//	Set cookie
	$cookie = "cookie/mmls_{$users->student_id}.cke";

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

	///////////
	///////////	First cURL to retrieve token
	//	URL of MMU Portal
	$url = "http://mmls.mmu.edu.my/";

	//	Is it a post request?
	$postRequest = FALSE;

	$mmlsData = mmlsInclude(array($url, $postRequest, 123499, $data = array()), $tokenClass);

	//	Get the token to login
	//	Load the string to HTML DOM without stripping /r/n tags
	$htmlDOM->load($mmlsData, true, false);

	//	Find the desired input field
	$inputToken = $htmlDOM->find('input[name=_token]');
	$mmlsToken = $inputToken[0]->value;

	//	Clear memory leak
	$htmlDOM->clear();

	///////////
	///////////	Second cURL to login to MMLS
	//	URL of MMU Portal
	$url = "https://mmls.mmu.edu.my/checklogin";

	//	Is it a post request?
	$postRequest = TRUE;

	//	Data to get Login Cookies
	$data = array('_token' => $mmlsToken, 'stud_id' => $studentID, 'stud_pswrd' => $password);

	$mmlsData = mmlsInclude(array($url, $postRequest, 123400, $data), $tokenClass, 1);

	messageSender(1, $mmlsData);


		
