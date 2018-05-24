<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 24/5/2018
	 * Time: 5:18 PM
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

	//	URL for account balance checking
	$url = "https://cms.mmu.edu.my/psc/csprd/EMPLOYEE/HRMS/c/SA_LEARNER_SERVICES.N_SSF_ACNT_SUMMARY.GBL?PORTALPARAM_PTCNAV=N_SSF_ACNT_SUMMARY_GBL&EOPP.SCNode=HRMS&EOPP.SCPortal=EMPLOYEE&EOPP.SCName=CO_EMPLOYEE_SELF_SERVICE&EOPP.SCLabel=Self%20Service&EOPP.SCPTfname=CO_EMPLOYEE_SELF_SERVICE&FolderPath=PORTAL_ROOT_OBJECT.CO_EMPLOYEE_SELF_SERVICE.HCCC_FINANCES.N_SSF_ACNT_SUMMARY_GBL&IsFolder=false&PortalActualURL=https%3a%2f%2fcms.mmu.edu.my%2fpsc%2fcsprd%2fEMPLOYEE%2fHRMS%2fc%2fSA_LEARNER_SERVICES.N_SSF_ACNT_SUMMARY.GBL&PortalContentURL=https%3a%2f%2fcms.mmu.edu.my%2fpsc%2fcsprd%2fEMPLOYEE%2fHRMS%2fc%2fSA_LEARNER_SERVICES.N_SSF_ACNT_SUMMARY.GBL&PortalContentProvider=HRMS&PortalCRefLabel=Account%20Enquiry&PortalRegistryName=EMPLOYEE&PortalServletURI=https%3a%2f%2fcms.mmu.edu.my%2fpsp%2fcsprd%2f&PortalURI=https%3a%2f%2fcms.mmu.edu.my%2fpsc%2fcsprd%2f&PortalHostNode=HRMS&NoCrumbs=yes&PortalKeyStruct=yes";

	//	It is a post request
	$postRequest = FALSE;

	//cURL
	$curl = NULL;

	//	Get account balance data for current trimester
	$curlResult = curl($curl, $url, $postRequest, $data = array(), $cookie);

	if (!$curlResult[0])
	{
		//	check account balance failed
		//	TODO ADD ERROR MESSAGE
		$this->error = 20601;

		return false;
	}

	//	Load the string to HTML DOM without stripping /r/n tags
	$htmlDOM->load($curlResult[1], true, false);

	//	Find the desired input field
	$balance = $htmlDOM->find('div[id=win0divDERIVED_SSF_MSG_SSF_MSG_LONG3]');

	//	Check if is "You have no outstanding charges at this time."
	if ($balance[0]->plaintext == "You have no outstanding charges at this time.")
	{
		$balance = 0;
	}
	else
	{
		$balance = $balance[0]->plaintext;
	}

	//	Clear the HTML DOM memory leak
	$htmlDOM->clear();

	//	Echo the message
	messageSender(1, $balance);