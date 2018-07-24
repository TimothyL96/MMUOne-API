<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 25/5/2018
	 * Time: 12:06 AM
	 */
	//	Get class schedule

	//	Require camsys helper
	require_once '../objects/camsys_helper.php';

	//	New Simple HTML DOM object
	$htmlDOM = new simple_html_dom();

	//	URL for getting class schedule
	$url = "https://cms.mmu.edu.my/psc/csprd/EMPLOYEE/HRMS/c/SA_LEARNER_SERVICES.SSR_SSENRL_SCHD_W.GBL?Page=SSR_SS_WEEK";
	//$url = "https://cms.mmu.edu.my/psc/csprd/EMPLOYEE/HRMS/c/SA_LEARNER_SERVICES.SSR_SSENRL_SCHD_W.GBL?Page=SSR_SS_WEEK&Action=A&ACAD_CAREER=UGRD&AS_OF_DATE=2018-02-02&INSTITUTION=MMU01&STRM=1810";
	//$url = "https://cms.mmu.edu.my/psc/csprd/EMPLOYEE/HRMS/c/SA_LEARNER_SERVICES.SSR_SSENRL_SCHD_W.GBL?Page=SSR_SS_WEEK&Action=A&ACAD_CAREER=UGRD&AS_OF_DATE=2018-06-02&INSTITUTION=MMU01&STRM=1810";

	//	It is a post request
	$postRequest = FALSE;

	//	Load cURL
	$camsysData = camsysInclude(array($url, $postRequest, 888888), $tokenClass);

	$htmlDOM->load($camsysData, TRUE, FALSE);

	$classSchedule = $htmlDOM->find("span.SSSTEXTWEEKLY");
	$classScheduleCount = count($classSchedule);

	//	Array to store processed class schedules
	$scheduleFinal = array();

	if ($classScheduleCount == 0)
	{
		$scheduleFinal = "No class for this week";
	}
	else
	{
		//	Insert into array
		foreach ($classSchedule as $class)
		{
			array_push($scheduleFinal, trim($class->plaintext));
		}
	}

	//	Echo all the class schedule
	messageSender(1, $scheduleFinal);

	//	Clear the HTML DOM memory leak after seeking with ->plaintext
	$htmlDOM->clear();

	//	TODO store in database