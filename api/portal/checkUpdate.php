<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 29/3/2018
	 * Time: 12:06 AM
	 */

	//	Headers
	require_once '../objects/header_get.php';

	//	Connection
	require_once '../config/connection.php';

	//	Users object
	require_once '../objects/users.php';

	//	Portal object
	require_once '../objects/portal.php';

	//	Include Message Sender function
	require_once '../objects/messageSender.php';

	//	Instantiate users object and retrieve connection
	$db = new Database();
	$conn = $db->connect();

	//	Set up Portal object
	$portal = new Portal($conn);

	//	Get $_GET data
	//	Check if tab provided
	if (empty($_GET['tab']))
	{
		//	TODO Set error

		//	Echo JSON message

		//	Kill
		die("No tab provided");
	}
	$tab = $_GET['tab'];

	//	Check if Student ID provided
	if (empty($_GET['student_id']))
	{
		//	TODO Set error

		//	Echo JSON message

		//	Kill
		die("No student ID specified");
	}
	$student_id = $_GET['student_id'];

	//	Check for any new updates for the bulletin news
	//	Get result
	$portal->getHash($tab);

	//	Echo in JSON format
	messageSender(1, $portal->hash);