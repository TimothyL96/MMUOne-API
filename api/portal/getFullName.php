<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 29/3/2018
	 * Time: 5:53 PM
	 */

	//	TODO TOKEN AUTHORIZATION
	//	TODO ADD COMMENTS
	//	TODO helper function

	//	"required" if htmlDOM is needed
	$htmlDOM = "required";
	require_once '../objects/portal_helper.php';

	//	Portal body
	$url = "https://online.mmu.edu.my/index.php";
	$portalData = portalInclude(array($url, FALSE, 12345), $tokenClass, $portal);

	//	Check return data
	if (!$portalData)
	{
		//	If false, means cURL failed
		messageSender(0, "Portal Data return error!", 11111);
		die();
	}

	//	Check for id "headerWrapper" that will contain "Welcome, (Full Name)"
	//	Load the string to HTML DOM without stripping /r/n tags
	$htmlDOM->load($portalData, true, false);

	//	Find the desired input field
	$inputFullName = $htmlDOM->find('#headerWrapper .floatL');

	//	Get the full name by filtering text at the front and back
	$fullName = trim(substr(trim($inputFullName[0]->plaintext), 8, strripos($inputFullName[0]->plaintext, "(") - 8));

	//	Echo the full name
	messageSender(1, array("message" => $fullName));

	//	Clear the DOM memory
	$htmlDOM->clear();