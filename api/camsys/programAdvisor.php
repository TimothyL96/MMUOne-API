<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 25/5/2018
	 * Time: 1:16 AM
	 */
	//	Show program advisor with email

	//	Require camsys helper
	require_once '../objects/camsys_helper.php';

	//	New Simple HTML DOM object
	$htmlDOM = new simple_html_dom();

	//	Get studen't program advisor
	$url = "https://cms.mmu.edu.my/psc/csprd/EMPLOYEE/HRMS/c/SA_LEARNER_SERVICES.SSR_SSADVR.GBL?PORTALPARAM_PTCNAV=HC_SSR_SSADVR_GBL&EOPP.SCNode=HRMS&EOPP.SCPortal=EMPLOYEE&EOPP.SCName=CO_EMPLOYEE_SELF_SERVICE&EOPP.SCLabel=Academic%20Records&EOPP.SCFName=HCCC_ACADEMIC_RECORDS&EOPP.SCSecondary=true&EOPP.SCPTfname=HCCC_ACADEMIC_RECORDS&FolderPath=PORTAL_ROOT_OBJECT.CO_EMPLOYEE_SELF_SERVICE.HCCC_ACADEMIC_RECORDS.HC_SSR_SSADVR_GBL&IsFolder=false&PortalActualURL=https%3a%2f%2fcms.mmu.edu.my%2fpsc%2fcsprd%2fEMPLOYEE%2fHRMS%2fc%2fSA_LEARNER_SERVICES.SSR_SSADVR.GBL&PortalContentURL=https%3a%2f%2fcms.mmu.edu.my%2fpsc%2fcsprd%2fEMPLOYEE%2fHRMS%2fc%2fSA_LEARNER_SERVICES.SSR_SSADVR.GBL&PortalContentProvider=HRMS&PortalCRefLabel=My%20Advisors&PortalRegistryName=EMPLOYEE&PortalServletURI=https%3a%2f%2fcms.mmu.edu.my%2fpsp%2fcsprd%2f&PortalURI=https%3a%2f%2fcms.mmu.edu.my%2fpsc%2fcsprd%2f&PortalHostNode=HRMS&NoCrumbs=yes&PortalKeyStruct=yes";

	$postRequest = FALSE;

	//	Load cURL
	$camsysData = camsysInclude(array($url, $postRequest, 111111), $tokenClass);

	//	Load the string to HTML DOM without stripping /r/n tags
	$htmlDOM->load($camsysData, true, false);

	//	Find the desired input field
	$advisorDetails = $htmlDOM->find('a[id=ADVR_NAME$0]');

	$advisorName = $advisorDetails[0]->plaintext;
	$advisorEmail = $advisorDetails[0]->href;

	//	Clear the mailto: infront of email
	$advisorEmail = substr($advisorEmail, 7, strlen($advisorEmail) - 1);

	//	Clear memory leak
	$htmlDOM->clear();

	messageSender(1, array("name" => $advisorName, "email" => $advisorEmail));

	//	TODO store in database