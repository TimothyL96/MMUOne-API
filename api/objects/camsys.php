<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 23/5/2018
	 * Time: 5:36 PM
	 */
	class camsys
	{
		//	Members for connection and table name
		private $conn;
		private $tableName = "camsys";

		// Constructor
		function __construct($db)
		{
			$this->conn = $db;
		}

		//	Destructor
		function __destruct()
		{

		}

		//	TODO insert and update row into camsys database
	}