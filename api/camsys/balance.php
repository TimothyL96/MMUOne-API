<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 24/5/2018
	 * Time: 5:18 PM
	 */
	//	Get account balance

	//	Require camsys helper
	require_once '../objects/camsys_helper.php';

	//	New Simple HTML DOM object
	$htmlDOM = new simple_html_dom();

	//	URL for account balance checking
	$url = "https://cms.mmu.edu.my/psc/csprd/EMPLOYEE/HRMS/c/SA_LEARNER_SERVICES.N_SSF_ACNT_SUMMARY.GBL?PORTALPARAM_PTCNAV=N_SSF_ACNT_SUMMARY_GBL&EOPP.SCNode=HRMS&EOPP.SCPortal=EMPLOYEE&EOPP.SCName=CO_EMPLOYEE_SELF_SERVICE&EOPP.SCLabel=Self%20Service&EOPP.SCPTfname=CO_EMPLOYEE_SELF_SERVICE&FolderPath=PORTAL_ROOT_OBJECT.CO_EMPLOYEE_SELF_SERVICE.HCCC_FINANCES.N_SSF_ACNT_SUMMARY_GBL&IsFolder=false&PortalActualURL=https%3a%2f%2fcms.mmu.edu.my%2fpsc%2fcsprd%2fEMPLOYEE%2fHRMS%2fc%2fSA_LEARNER_SERVICES.N_SSF_ACNT_SUMMARY.GBL&PortalContentURL=https%3a%2f%2fcms.mmu.edu.my%2fpsc%2fcsprd%2fEMPLOYEE%2fHRMS%2fc%2fSA_LEARNER_SERVICES.N_SSF_ACNT_SUMMARY.GBL&PortalContentProvider=HRMS&PortalCRefLabel=Account%20Enquiry&PortalRegistryName=EMPLOYEE&PortalServletURI=https%3a%2f%2fcms.mmu.edu.my%2fpsp%2fcsprd%2f&PortalURI=https%3a%2f%2fcms.mmu.edu.my%2fpsc%2fcsprd%2f&PortalHostNode=HRMS&NoCrumbs=yes&PortalKeyStruct=yes";

	//	It is a post request
	$postRequest = FALSE;

	//	Load cURL
	$camsysData = camsysInclude(array($url, $postRequest, 123123123), $tokenClass);

	Log.d($camsysData);
	//	Load the string to HTML DOM without stripping /r/n tags
	$htmlDOM->load($camsysData, true, false);

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