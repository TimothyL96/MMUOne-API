<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 09/6/2018
	 * Time: 12:55 AM
	 */

	//	Require the token class
	require_once 'token.php';

	//	Require the database class
	require_once '../config/connection.php';

	//	Require the tokenStore class
	require_once '../objects/tokenStore.php';

	$db = new Database();
	$conn = $db->connect();

	//	Check whether input token is valid or not
	//	Return student ID
	function tokenValidation($tokenToValidate, $token)
	{
		$studentIDFromDB = $token->getStudentID($tokenToValidate);
		if (!$studentIDFromDB)
		{
			return FALSE;
		}

		return $token->student_id;
	}

	function tokenGeneration($studentID, $token)
	{
		//	Obtain secret key
		require_once '../../../secret.php';

		$macAddr = $token->getMacAddr();
		$dateTime = date("Y-m-d H:i:s");

		//	Generate token
		$tokenHash = hash_hmac('sha256', $studentID . $macAddr . $dateTime, $secretKey);

		//	Store in token store
		tokenStore::$token = $tokenHash;

		//	Store token and time
		return $token->updateTable($tokenHash, $dateTime);
	}

	function macAddrUpdate($studentID, $macAddr, $token)
	{
		//	Set student ID
		//$token->student_id = $studentID;

		return $token->updateMacAddr($macAddr);
	}

	function checkUserExist($studentID, $token)
	{

		//	Set student ID
		//$token->student_id = $studentID;

		return $token->insertNewUser();
	}