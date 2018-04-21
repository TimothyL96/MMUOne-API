<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 29/3/2018
	 * Time: 12:13 AM
	 */

	//	Header
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
	require_once '../objects/portal.php';

	//	Get Simple HTML DOM library
	require_once '../library/html_dom.php';

	//	New Simple HTML DOM object
	$htmlDOM = new simple_html_dom();

	//	Instantiate users object and retrieve connection
	$db = new Database();
	$conn = $db->connect();

	//	Set up Portal object
	$portal = new Portal($conn);

	//	Set error
	$error = 000000;

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

	//	Check if cookie path provided
	if (empty($_GET['cookie']))
	{
		//	TODO Set error and login

		//	Echo JSON message

		//	Kill
		die("No student ID specified");
	}
	$cookie = $_GET['cookie'];

	//	Check if token provided
	//	Token equals to page number
	if (!empty($_GET['token']))
	{
		$token = $_GET['token'];
	}

	if (empty($token))
	{
		//	If time getting data, no token exist
		//	Get all the bulletin news
		//	Get bulletin with specific page: Page 0 for 1-10 news, 1 for 11-20 news
		//	URL of MMU Portal's Bulletion Boarx
		$url = "https://online.mmu.edu.my/bulletin.php";

		//	cURL
		$curl = NULL;

		//	It is not a POST request
		$postRequest = FALSE;

		//	Execute cURL requets
		$curlResult = curl($curl, $url, $postRequest, $cookie);

		//	Set bulletin paged array
		//	bulletin contains max 9 news, page 0 for no pages 1 for more pages
		$bulletinPaged["bulletin"] = array();
		$bulletinPaged["hasPage"] = 0;
		$bulletinPaged["size"] = 0;

		if (!$curlResult[0])
		{
			$errorMessage = $curlResult[1];

			//	TODO ADD ERROR MESSAGE
			//	Get bulletin failed
			$error = 20602;

			// TODO echo error
		}
		else if ($curlResult[0])
		{
			//	Load the string to HTML DOM without stripping /r/n tags
			$htmlDOM->load($curlResult[1], TRUE, FALSE);

			//	Find the desired input field
			$bulletin = $htmlDOM->find("div[id=tabs-{$tab}] div.bulletinContentAll");

			//	Get old hash
			$portal->getHash($student_id, $tab);
			$oldHash = $portal->hash;

			//	Get latest hash
			$latestHash = hash('sha256', $bulletin[0]->plaintext);

			//	Set the latest bulletin news
			foreach ($bulletin as $key => $bulletinSingle)
			{
				//	Get new hash
				$currentHash = hash('sha256', $bulletinSingle->plaintext);

				//	If current new news is already in the database, return
				if ($oldHash == $currentHash)
				{
					break;
				}
				else
				{
					//	Push the plaintext into bulletinPaged's bulletin
					array_push($bulletinPaged["bulletin"], $bulletinSingle->plaintext);

					//	Increment the bulletin size by 1
					$bulletinPaged["size"] = $bulletinPaged["size"] + 1;

					//	If max key reached
					if ($key == 9)
					{
						//	Set more pages to true or 1
						$bulletinPaged["hasPage"] = 1;

						//	Break the foreach loop
						break;
					}
				}
			}
			//	Update table with data and latest hash
			$portal->updateTable($student_id, $tab, json_encode($bulletin), $latestHash);
		}

		//	Echo result as JSON
		messageSender(1, $bulletinPaged);
	}
	else
	{
		//	If token exist, get next page of data
		// TODO get bulletin data from database
		//	TODO accept token for new page data
	}
