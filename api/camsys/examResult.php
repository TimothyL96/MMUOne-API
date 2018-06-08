<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 25/5/2018
	 * Time: 12:26 AM
	 */
	//	Get exam result

	//	Headers
	require_once '../objects/header_get.php';

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

	//	Get exam result for current trimester
	$url = "https://cms.mmu.edu.my/psc/csprd/EMPLOYEE/HRMS/c/N_MANAGE_EXAMS.N_RSLT_SLIP_COMP.GBL?PORTALPARAM_PTCNAV=N_RSLT_SLIP_COMP_GBL&EOPP.SCNode=HRMS&EOPP.SCPortal=EMPLOYEE&EOPP.SCName=CO_EMPLOYEE_SELF_SERVICE&EOPP.SCLabel=Self%20Service&EOPP.SCPTfname=CO_EMPLOYEE_SELF_SERVICE&FolderPath=PORTAL_ROOT_OBJECT.CO_EMPLOYEE_SELF_SERVICE.N_RSLT_SLIP_COMP_GBL&IsFolder=false&PortalActualURL=https%3a%2f%2fcms.mmu.edu.my%2fpsc%2fcsprd%2fEMPLOYEE%2fHRMS%2fc%2fN_MANAGE_EXAMS.N_RSLT_SLIP_COMP.GBL&PortalContentURL=https%3a%2f%2fcms.mmu.edu.my%2fpsc%2fcsprd%2fEMPLOYEE%2fHRMS%2fc%2fN_MANAGE_EXAMS.N_RSLT_SLIP_COMP.GBL&PortalContentProvider=HRMS&PortalCRefLabel=Result%20Slip&PortalRegistryName=EMPLOYEE&PortalServletURI=https%3a%2f%2fcms.mmu.edu.my%2fpsp%2fcsprd%2f&PortalURI=https%3a%2f%2fcms.mmu.edu.my%2fpsc%2fcsprd%2f&PortalHostNode=HRMS&NoCrumbs=yes&PortalKeyStruct=yes";