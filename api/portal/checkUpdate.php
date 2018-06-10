<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 29/3/2018
	 * Time: 12:06 AM
	 */

	require_once '../objects/portal_helper.php';

	$portalData = portalInclude(array(), array());

	//	Check return data
	if (!$portalData)
	{
		//	If false, means cURL failed
		messageSender(0, "Portal Data return error!", 11111);
		die();
	}

	//	Check for any new updates for the bulletin news
	//	Get result
	$portal->getHash($tab);

	//	Echo in JSON format
	messageSender(1, $portal->hash);