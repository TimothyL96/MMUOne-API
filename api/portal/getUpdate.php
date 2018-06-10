<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 29/3/2018
	 * Time: 12:13 AM
	 */

	$htmlDOM = "required";

	require_once '../objects/portal_helper.php';

	//	force_update is optional, default is 0 or no force update
	//	If a token number is given, force_update has no effect
	$forcedUpdate = false;
	if (!empty($_GET['force_update']))
	{
		$forcedUpdate = (bool)$_GET['force_update'];
	}

	//	Check if page number provided
	if (!empty($_GET['token']))
	{
		$page = $_GET['token'];
	}

	//	Set bulletin paged array
	//	bulletin contains max 9 news, page 0 for no pages 1 for more pages
	$bulletinPaged["bulletin"] = array();
	$bulletinPaged["hasPage"] = 0;
	$bulletinPaged["size"] = 0;
	$bulletinPaged["token"] = 0;

	if (empty($page))
	{
		//	If time getting data, no token exist
		//	Get all the bulletin news
		//	Get bulletin with specific page: Page 0 for 1-10 news, 1 for 11-20 news
		//	URL of MMU Portal's Bulletion Boarx
		$url = "https://online.mmu.edu.my/bulletin.php";

		//	Get cURL result
		$portalData = portalInclude(array(), array($url, FALSE, 54321), $tokenClass);

		//	Check return data
		if (!$portalData)
		{
			//	If false, means cURL failed
			messageSender(0, "Portal Data return error!", 11111);
			die();
		}

		//	If bulletin data retrieved successfully
		//	Load the string to HTML DOM without stripping /r/n tags
		$htmlDOM->load($portalData, TRUE, FALSE);

		//	Find the desired input field
		$bulletin = $htmlDOM->find("div[id=tabs-{$tab}] div.bulletinContentAll");

		if (empty($_GET['hash']))
		{
			//	Get old hash
			$portal->getHash($tab);
			$oldHash = $portal->hash;
		}
		else
		{
			$oldHash = $_GET['hash'];
		}

		//	Get latest hash
		$latestHash = hash('sha256', $bulletin[0]->plaintext);

		//	Set the latest bulletin news
		foreach ($bulletin as $key => $bulletinSingle)
		{
			//	Get new hash
			$currentHash = hash('sha256', $bulletinSingle->plaintext);

			//	If current new news is already in the database, return
			//	If this is not forced update, return
			if ($oldHash == $currentHash && !$forcedUpdate)
			{
				break;
			}
			else
			{
				//	Push the plaintext into bulletinPaged's bulletin
				array_push($bulletinPaged["bulletin"], $bulletinSingle->plaintext);

				//	Increment the bulletin size by 1
				$bulletinPaged["size"] = $bulletinPaged["size"] + 1;

				//	Token is the total size sent
				$bulletinPaged["token"] = $bulletinPaged["token"] + 1;

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

		$bulletinAll = array();

		foreach ($bulletin as $key => $bulletinSingle)
		{
			$bulletinAll[$key] = $bulletinSingle->plaintext;
		}

		//	Clear the htmlDOM memory
		$htmlDOM->clear();

		//	Update table with data and latest hash
		$portal->updateTable($tab, json_encode($bulletinAll), $latestHash);

	}
	else
	{
		//	If token exist, get next page of data and echo as JSON
		//	$token is total bulletin size sent
		//	Set the bulletin token
		$bulletinPaged["token"] = $page;

		//	Get bulletin data
		$bulletin = $portal->getBulletin($tab);

		//	TODO check if data retrieval succeeded
		if (!$bulletin)
		{
			messageSender(0, "\$bulletin data retrieval error", 1234789);
			die();
		}
		$bulletin = json_decode(html_entity_decode($portal->data));

		//	Load the string to HTML DOM without stripping /r/n tags
		//$htmlDOM->load($bulletinRetrieved, TRUE, FALSE);

		//	Find the desired input field
		//$bulletin = $htmlDOM->find("div[id=tabs-{$tab}] div.bulletinContentAll");

		//	Counter to skip the bulletin data that are already sent
		$pageCount = 0;

		//	Get the end key
		end($bulletin);
		$lastKey = key($bulletin);

		//	Set the next 10 bulletin data
		foreach ($bulletin as $key => $bulletinSingle)
		{
			if ($pageCount < $page)
			{
				//	Increment the counter
				$pageCount++;
				continue;
			}

			//	Push the plaintext into bulletinPaged's bulletin
			array_push($bulletinPaged["bulletin"], htmlentities($bulletinSingle));

			//	Increment the bulletin size by 1
			$bulletinPaged["size"] = $bulletinPaged["size"] + 1;

			//	Token is the total size sent
			$bulletinPaged["token"] = $bulletinPaged["token"] + 1;

			//	If max key reached
			if ($key - $page == 9 && $key != $lastKey)
			{
				//	Set more pages to true or 1
				$bulletinPaged["hasPage"] = 1;

				//	Break the foreach loop
				break;
			}
		}
	}

	//	Echo result as JSON
	//	-	bulletin data
	//	-	hasPage
	//	-	size
	messageSender(1, $bulletinPaged);
