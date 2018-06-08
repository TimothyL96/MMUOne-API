<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 09/6/2018
	 * Time: 12:55 AM
	 */

	//	Require the token class
	require_once 'token.php';

	//	Require the connection class
	require_once '../config/connection.php';

	//	Check whether input token is valid or not
	function tokenValidation($studentID, $tokenToValidate)
	{
		$db = new Database();
		$conn = $db->connect();

		$token = new token($conn);
		$token->student_id = $studentID;

		$tokenFromDB = $token->getToken();
		if (!$tokenFromDB)
		{
			return FALSE;
		}

		//	Same token
		if ($tokenFromDB == $tokenToValidate)
		{
			//	In the future, can add token duration/time checking
			return TRUE;
		}

		return FALSE;
	}