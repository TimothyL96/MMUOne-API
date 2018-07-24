<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 11/6/2018
	 * Time: 5:16 PM
	 */

	//	Get all HTTP headers
	$headers = apache_request_headers();

	//	Check if authorization header set
	if (!isset($headers['Authorization']))
	{
		//	Invalid request - echo error in JSON and die
		messageSender(0, "No token received", 123456);
		die();
	}

	//	Retrieve token
	$token = $headers['Authorization'];

	if ($toLogin)
	{
		$token = tokenStore::$token;
	}

	//	Peform token validation - Retrieve student ID from related token
	$student_id = tokenValidation($token, $tokenClass);
	if (!$student_id)
	{
		messageSender(0, "Invalid token received", 123457);
		die();
	}

	tokenGeneration($student_id, $tokenClass);