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
		public $student_id;
		public $macAddr;
		public $token;

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

		function insertNewUser()
		{
			$query = "SELECT * FROM {$this->tableName} WHERE student_id = ?";

			$stmt = $this->conn->prepare($query);

			$stmt->bindParam(1, $this->student_id);

			if (!$stmt->execute())
			{
				return FALSE;
			}

			if ($stmt->rowCount() == 1)
			{
				return TRUE;
			}

			//	Row does not exist, create new row
			$query = "INSERT INTO {$this->tableName} (student_id) VALUES (?)";

			$stmt = $this->conn->prepare($query);

			$stmt->bindParam(1, $this->student_id);

			if (!$stmt->execute())
			{
				return FALSE;
			}

			return TRUE;
		}

		function getMacAddr()
		{
			$query = "SELECT mac_addr FROM {$this->tableName} WHERE student_id = ?";

			$stmt = $this->conn->prepare($query);

			$stmt->bindParam(1, $this->student_id);

			if (!$stmt->execute())
			{
				return FALSE;
			}

			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			$this->macAddr = $row['mac_addr'];

			return TRUE;
		}

		function getToken()
		{
			$query = "SELECT token FROM {$this->tableName} WHERE student_id = ?";

			$stmt = $this->conn->prepare($query);

			$stmt->bindParam(1, $this->student_id);

			if (!$stmt->execute())
			{
				return FALSE;
			}

			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			$this->token = $row['token'];

			return TRUE;
		}

		function getStudentID($token)
		{
			$query = "SELECT student_id FROM {$this->tableName} WHERE token = ?";

			$stmt = $this->conn->prepare($query);

			$stmt->bindParam(1, $token);

			if (!$stmt->execute())
			{
				return FALSE;
			}

			$count = $stmt->rowCount();

			if ($count == 1)
			{
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$this->student_id = $row['student_id'];

				return TRUE;
			}

			return FALSE;
		}

		function updateMacAddr($macAddr)
		{
			$query = "UPDATE {$this->tableName} SET mac_addr = ? WHERE student_id = ?";

			$stmt = $this->conn->prepare($query);

			$stmt->bindParam(1, $macAddr);
			$stmt->bindParam(2, $this->student_id);

			if (!$stmt->execute())
			{
				return FALSE;
			}

			return TRUE;
		}

		//	Update token and date time
		function updateTable($token, $dateTime)
		{
			$query = "UPDATE {$this->tableName} SET token = :token, datetime = :dateTime WHERE student_id = :studentID";

			$stmt = $this->conn->prepare($query);

			$stmt->bindParam(":token", $token);
			$stmt->bindParam(":dateTime", $dateTime);
			$stmt->bindParam(":studentID", $this->student_id);

			if (!$stmt->execute())
			{
				return FALSE;
			}

			return TRUE;
		}
	}