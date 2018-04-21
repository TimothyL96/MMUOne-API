<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 29/3/2018
	 * Time: 5:26 PM
	 */

	/**
	 * @param     $status
	 * @param     $data
	 * @param int $errorCode
	 * @return array
	 */
	function messageSender($status, $data, $errorCode = 0)
	{
		$message = array(
			"status" => $status,
			"code" => $errorCode,
			"message" => $data
		);

		echo $message;
	}