<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 09/6/2018
	 * Time: 1:32 AM
	 */
	class token
	{
		//	Members for connection and table name
		private $conn;
		private $tableName = "token";

		//	Public members

		/**
		 * Portal constructor. Get database connection and put in the private class member for connection
		 *
		 * @param $db
		 */
		public function __construct($db)
		{
			//	Retrieve database connection
			$this->conn = $db;
		}

		/**
		 * Destructor
		 */
		public function __destruct()
		{

		}

	}