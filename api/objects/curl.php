<?php
	/**
	 * This function is responsible for sending network request
	 * This function will return an array of status and result
	 * status:	"1 for succeed" or "0 for failed"
	 *
	 * curl() takes in 5 parameters:
	 * curl 		-	the cURL object from the calling object
	 * url			-	URL of the website intended to be surfed
	 * postRequest	-	TRUE if the request is a POST request or FALSE if it is a GET request
	 * data 		-	The data to be sent to the server, if any
	 * cookie		-	The cookie file for the cookie to be stored and read from
	 *
	 * @param       $curl
	 * @param       $url
	 * @param       $postRequest
	 * @param array $data
	 * @param       $cookie
	 * @return array
	 */

	function curl($curl, $url, $postRequest, $data = array(), $cookie)
	{
		//	Connect to MMU PORTAL with cURL
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, $postRequest);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.93 Safari/537.36");
		curl_setopt($curl, CURLOPT_HEADER, FALSE);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);

		//	Execute the CURL operation and get the results
		$result = curl_exec($curl);

		//	Set default status to succeeded
		$status = 1;

		//	If error exist, show error
		if (!empty(curl_error($curl)))
		{
			$status = 0;
			$result = curl_error($curl);
		}
		
		//	Return result
		return array($status, $result);
	}