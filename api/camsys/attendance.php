<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 24/5/2018
	 * Time: 2:31 PM
	 */
	//	Get class attendance

	//	Require camsys helper
	require_once '../objects/camsys_helper.php';

	//	New Simple HTML DOM object
	$htmlDOM = new simple_html_dom();

	//	Store the attendance data
	$attendance = array();
	$attendanceFinal = array();

	//	URL for attendance checking
	$url = "https://cms.mmu.edu.my/psc/csprd/EMPLOYEE/HRMS/c/N_SR_STUDENT_RECORDS.N_SR_SS_ATTEND_PCT.GBL?PortalActualURL=https%3a%2f%2fcms.mmu.edu.my%2fpsc%2fcsprd%2fEMPLOYEE%2fHRMS%2fc%2fN_SR_STUDENT_RECORDS.N_SR_SS_ATTEND_PCT.GBL&PortalContentURL=https%3a%2f%2fcms.mmu.edu.my%2fpsc%2fcsprd%2fEMPLOYEE%2fHRMS%2fc%2fN_SR_STUDENT_RECORDS.N_SR_SS_ATTEND_PCT.GBL&PortalContentProvider=HRMS&PortalCRefLabel=Attendance%20Percentage%20by%20class&PortalRegistryName=EMPLOYEE&PortalServletURI=https%3a%2f%2fcms.mmu.edu.my%2fpsp%2fcsprd%2f&PortalURI=https%3a%2f%2fcms.mmu.edu.my%2fpsc%2fcsprd%2f&PortalHostNode=HRMS&NoCrumbs=yes&PortalKeyStruct=yes";

	//	It is a post request
	$postRequest = FALSE;

	//	Load cURL
	$camsysData = camsysInclude(array($url, $postRequest, 79797979), $tokenClass);

	//	Load the string to HTML DOM without stripping /r/n tags
	$htmlDOM->load($camsysData, true, false);

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
	//	TODO check if checked in lecture barring list <input checked/>
	for ($i = 3; $i < $subjectCount; $i++)
	{
		$attendanceSubjectPlaintext = $attendanceTable[$i]->plaintext;
		$attendance[substr($attendanceSubjectPlaintext, 0, strpos($attendanceSubjectPlaintext, ' '))] = trim(substr($attendanceSubjectPlaintext, strpos($attendanceSubjectPlaintext, ' ')));
	}

	//	Clear the Simple HTML DOM library memory leak
	$htmlDOM->clear();

	foreach ($attendance as $attendanceSubject)
	{
		$attendanceSubject = explode(' ', $attendanceSubject);

		//	Trim
		$attendanceSubject = array_map('trim', $attendanceSubject);

		array_push($attendanceFinal, $attendanceSubject);
	}

	//	$attendanceFinal:
	//	0: MPU
	//	1: 3113
	//	2: Lecture/Tutorial
	//	3: Subject Name
	//	Last 5th: Current Attendance %
	//	Last 4rd: Barring Process Attendance %
	//	Last 3rd - Last Updated By: MU070320
	//	Last 2nd - Date: 11/05/2018
	//	Last - Time: 11:30:17AM

	//	TODO store in database

	//	Echo the attendance
	messageSender(1, $attendanceFinal);

	//	TODO check if login timedout