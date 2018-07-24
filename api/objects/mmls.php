<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 24/7/2018
	 * Time: 10:24 PM
	 */
	class mmls
	{
		private $conn;
		private $tableName = "mmls";

		public $student_id;

		//	Constructor
		function __construct($db)
		{
			$this->conn = $db;
		}

		function __destruct()
		{

		}


	}