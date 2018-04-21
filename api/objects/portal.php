<?php
	/**
	 * Created by PhpStorm.
	 * User: Timothy
	 * Date: 29/3/2018
	 * Time: 5:01 PM
	 */

	class portal
	{
		//	Members for connection and table name
		private $conn;
		private $tableName = "portal";

		//	Members for columns in the table "Portal"
		public $hash;

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

		function getHash($student_id, $tab)
		{
			//	Select hash of given tab of specific student
			$query = "SELECT hash FROM {$this->tableName} WHERE tab = :tab AND student_id = :student_id";

			//	Prepare query statement
			$stmt = $this->conn->prepare($query);

			//	Bind parameters
			$stmt->bindParam(':tab', $tab);
			$stmt->bindParam(':student_id', $student_id);

			//	Execute query
			if (!$stmt->execute())
			{
				return false;
			}

			//	Get retrieved row
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			$this->hash = $row['hash'];
			
			//	Store result
			return TRUE;
		}

		function updateHash($student_id, $tab, $hash)
		{
			//	Update query
			$query = "UPDATE {$this->tableName} SET hash = :hash WHERE tab = :tab AND student_id = :student_id";

			//	Prepare query statement
			$stmt = $this->conn->prepare($query);

			//	Sanitize
			$student_id = htmlspecialchars(strip_tags($student_id));
			$tab = htmlspecialchars(strip_tags($tab));
			$hash = htmlspecialchars(strip_tags($hash));

			//	Bind new values
			$stmt->bindParam(':student_id', $student_id);
			$stmt->bindParam(':tab', $tab);
			$stmt->bindParam(':hash', $hash);

			//	Execute query
			if ($stmt->execute())
			{
				return true;
			}

			return false;
		}

		function updateTable($student_id, $tab, $data, $hash)
		{
			//	Update data and hash
			$query = "UPDATE {$this->tableName} SET data = :data, hash = :hash WHERE tab = :tab AND student_id = :student_id";

			//	Prepare query statement
			$stmt = $this->conn->prepare($query);

			//	Sanitize
			$student_id = htmlspecialchars(strip_tags($student_id));
			$tab = htmlspecialchars(strip_tags($tab));
			$data = htmlspecialchars(strip_tags($data));
			$hash = htmlspecialchars(strip_tags($hash));

			//	Bind new values
			$stmt->bindParam(':student_id', $student_id);
			$stmt->bindParam(':tab', $tab);
			$stmt->bindParam(':data', $data);
			$stmt->bindParam(':hash', $hash);

			//	Execute query
			if ($stmt->execute())
			{
				return true;
			}

			return false;
		}
	}