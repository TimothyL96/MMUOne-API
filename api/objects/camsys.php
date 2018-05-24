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

		//	Update the table
		function updateTable($student_id)
		{
			//	Update query
			$query = "UPDATE {$this->tableName} SET hash = :hash WHERE student_id = :student_id";

			//	Prepare query statement
			$stmt = $this->conn->prepare($query);

			//	Sanitize
			$student_id = htmlspecialchars(strip_tags($student_id));

			//	Bind new values
			$stmt->bindParam(':student_id', $student_id);

			//	Execute query
			if ($stmt->execute())
			{
				return true;
			}

			return false;
		}

		//	Insert row
		function newUser()
		{

		}

		//	TODO insert and update row into camsys database
	}