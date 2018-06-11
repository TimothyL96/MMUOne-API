<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 29/3/2018
	 * Time: 12:06 AM
	 */

	require_once '../objects/portal_helper.php';

	$portalData = portalInclude(array(), $tokenClass, 	$portal);

	//	Check return data
	if (!$portalData)
	{
		//	If false, means cURL failed
		messageSender(0, "Portal Data return error!", 11111);
		die();
	}

	//	Get $_GET data
	//	Check if tab provided
	if (empty($_GET['tab']))
	{
		//	TODO Set error

		//	Echo JSON message
		messageSender(0, "No tab provided", 123457);

		//	Kill
		die();
	}
	$tab = $_GET['tab'];

	//	Check for any new updates for the bulletin news
	//	Get result
	$portal->getHash($tab);

	//	Echo in JSON format
	messageSender(1, array("hash" => $portal->hash));