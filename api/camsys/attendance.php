<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 24/5/2018
	 * Time: 2:31 PM
	 */

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
	require_once '../objects/camsys.php';

	//	Get Simple HTML DOM library
	require_once '../library/html_dom.php';

	//	Include cURL function: curl(url, postRequest, data, cookie)
	require_once '../objects/curl.php';

	//	Include Message Sender function
	require_once '../objects/messageSender.php';

	//	Instantiate users object and retrieve connection
	$db = new Database();
	$conn = $db->connect();

	//	Set connection for users table
	$users = new Users($conn);

	//	Set up Portal object
	$camsys = new camsys($conn);

	//	New Simple HTML DOM object
	$htmlDOM = new simple_html_dom();

	//	Set error
	$error = 00000;

	//	Store the attendance data
	$attendance = array();

	//	Check if Student ID provided
	if (empty($_GET['student_id']))
	{
		//	TODO Set error

		//	Echo JSON message

		//	Kill
		die("No student ID specified");
	}

	//	Set the student ID
	$users->student_id = $_GET['student_id'];

	//	Set cookie
	$cookie = "cookie/camsys_{$users->student_id}.cke";

	//	URL for attendance checking
	$url = "https://cms.mmu.edu.my/psc/csprd/EMPLOYEE/HRMS/c/N_SR_STUDENT_RECORDS.N_SR_SS_ATTEND_PCT.GBL?PortalActualURL=https%3a%2f%2fcms.mmu.edu.my%2fpsc%2fcsprd%2fEMPLOYEE%2fHRMS%2fc%2fN_SR_STUDENT_RECORDS.N_SR_SS_ATTEND_PCT.GBL&PortalContentURL=https%3a%2f%2fcms.mmu.edu.my%2fpsc%2fcsprd%2fEMPLOYEE%2fHRMS%2fc%2fN_SR_STUDENT_RECORDS.N_SR_SS_ATTEND_PCT.GBL&PortalContentProvider=HRMS&PortalCRefLabel=Attendance%20Percentage%20by%20class&PortalRegistryName=EMPLOYEE&PortalServletURI=https%3a%2f%2fcms.mmu.edu.my%2fpsp%2fcsprd%2f&PortalURI=https%3a%2f%2fcms.mmu.edu.my%2fpsc%2fcsprd%2f&PortalHostNode=HRMS&NoCrumbs=yes&PortalKeyStruct=yes";

	//	It is a post request
	$postRequest = FALSE;

	//cURL
	$curl = NULL;

	//	Get attendance data for current trimester
	$curlResult = curl($curl, $url, $postRequest, $data = array(), $cookie);

	if (!$curlResult[0])
	{
		//	check attendance failed
		//	TODO ADD ERROR MESSAGE
		$this->error = 20601;

		return false;
	}

	//	Load the string to HTML DOM without stripping /r/n tags
	$htmlDOM->load($curlResult[1], true, false);

	//	Find the desired input field
	$attendanceTable = $htmlDOM->find('table.PSLEVEL1GRIDWBO tr');

	//	Get the total number of subject with attendance
	$subjectCount = count($attendanceTable);

	//	Process and retrieve the attendance
	//	Retrieve Subject area and Subject/Catalogue. Ex: MPU 3113
	//	Retrieve Course component. Ex: Lecture/Tutorial
	//	Retrieve Course Description. Ex: HUBUNGAN ETNIK
	//	Retrieve HTML Input Checkbox for Lecturer's Barring List
	//	Retrieve Current attendance % and Barring Process Attendance %
	//	Retrieve Last Updated. Ex: 11/05/2018 11:30:17AM

	for ($i = 3; $i < $subjectCount; $i++)
	{
		$attendanceSubjectPlaintext = $attendanceTable[$i]->plaintext;
		$attendance[substr($attendanceSubjectPlaintext, 0, strpos($attendanceSubjectPlaintext, ' '))] = substr($attendanceSubjectPlaintext, strpos($attendanceSubjectPlaintext, ' '));
	}

	//	Clear the Simple HTML DOM library memory leak
	$htmlDOM->clear();

	//	TODO store in database

	//	Echo the attendance
	messageSender(1, $attendance);

	//	TODO check if login timedout