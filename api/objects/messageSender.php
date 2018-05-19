<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 29/3/2018
	 * Time: 5:26 PM
	 */

	/**
	 * @param     $status success or failed
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

		//	Echo the opening { for JSON output
		echo "{";

		//	Coverting array's keys and values to JSON with comma
		foreach ($message as $key => $value)
		{
			//	Echo the key and value
			echo "\"{$key}\": ";

			if (is_array($value))
			{
				echo "{";
				foreach ($value as $key1 => $value1)
				{
					//	Echo the key and value
					echo "\"{$key1}\": \"{$value1}\"";

					if ((string)end($value) != (string)$value1)
					{
						echo ",";
					}
				}
				echo "}";
			}
			else
			{
				echo "\"{$value}\"";
			}

			//	If not end of message, echo comma (,)
			if ((string)end($message) != (string)$value)
			{
				echo ",";
			}
		}

		//	Echo the closing } for JSON output
		echo "}";
	}