<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 25/5/2018
	 * Time: 1:16 AM
	 */
	//	Show program advisor with email

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

	//	Get studen't program advisor
	$url = "https://cms.mmu.edu.my/psc/csprd/EMPLOYEE/HRMS/c/SA_LEARNER_SERVICES.SSR_SSADVR.GBL?PORTALPARAM_PTCNAV=HC_SSR_SSADVR_GBL&EOPP.SCNode=HRMS&EOPP.SCPortal=EMPLOYEE&EOPP.SCName=CO_EMPLOYEE_SELF_SERVICE&EOPP.SCLabel=Academic%20Records&EOPP.SCFName=HCCC_ACADEMIC_RECORDS&EOPP.SCSecondary=true&EOPP.SCPTfname=HCCC_ACADEMIC_RECORDS&FolderPath=PORTAL_ROOT_OBJECT.CO_EMPLOYEE_SELF_SERVICE.HCCC_ACADEMIC_RECORDS.HC_SSR_SSADVR_GBL&IsFolder=false&PortalActualURL=https%3a%2f%2fcms.mmu.edu.my%2fpsc%2fcsprd%2fEMPLOYEE%2fHRMS%2fc%2fSA_LEARNER_SERVICES.SSR_SSADVR.GBL&PortalContentURL=https%3a%2f%2fcms.mmu.edu.my%2fpsc%2fcsprd%2fEMPLOYEE%2fHRMS%2fc%2fSA_LEARNER_SERVICES.SSR_SSADVR.GBL&PortalContentProvider=HRMS&PortalCRefLabel=My%20Advisors&PortalRegistryName=EMPLOYEE&PortalServletURI=https%3a%2f%2fcms.mmu.edu.my%2fpsp%2fcsprd%2f&PortalURI=https%3a%2f%2fcms.mmu.edu.my%2fpsc%2fcsprd%2f&PortalHostNode=HRMS&NoCrumbs=yes&PortalKeyStruct=yes";