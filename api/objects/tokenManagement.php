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
	//	Return student ID
	function tokenValidation($tokenToValidate)
	{
		$db = new Database();
		$conn = $db->connect();

		$token = new token($conn);

		$studentIDFromDB = $token->getStudentID();
		if (!$studentIDFromDB)
		{
			return FALSE;
		}

		return $studentIDFromDB;
	}

	function tokenGeneration($studentID)
	{
		//	Obtain secret key
		require_once '../../../secret.php';

		$db = new Database();
		$conn = $db->connect();

		$token = new token($conn);

		//	Set student ID
		$token->student_id = $studentID;

		$macAddr = $token->getMacAddr();
		$dateTime = date("Y-m-d H:i:s");

		//	Generate token
		$tokenHash = hash_hmac('sha256', $studentID . $macAddr . $dateTime, $secretKey);

		//	Store in token store
		tokenStore::$token = $tokenHash;

		//	Store token and time
		return $token->updateTable($tokenHash, $dateTime);
	}

	function macAddrUpdate($studentID, $macAddr)
	{
		$db = new Database();
		$conn = $db->connect();

		$token = new token($conn);

		//	Set student ID
		$token->student_id = $studentID;

		return $token->updateMacAddr($macAddr);
	}

	function checkUserExist($studentID)
	{
		$db = new Database();
		$conn = $db->connect();

		$token = new token($conn);

		//	Set student ID
		$token->student_id = $studentID;

		return $token->insertNewUser();
	}