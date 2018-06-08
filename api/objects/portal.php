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
		public $data;
		public $fullName;
		public $student_id;

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

		function getHash($tab)
		{
			//	Select hash of given tab of specific student
			$query = "SELECT hash FROM {$this->tableName} WHERE tab = :tab AND student_id = :student_id";

			//	Prepare query statement
			$stmt = $this->conn->prepare($query);

			//	Bind parameters
			$stmt->bindParam(':tab', $tab);
			$stmt->bindParam(':student_id', $this->student_id);

			//	Execute query
			if (!$stmt->execute())
			{
				return FALSE;
			}

			//	Get retrieved row
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			//	Set data to class member
			$this->hash = $row['hash'];
			
			//	Get data successful
			return TRUE;
		}

		function getFullName()
		{
			$query = "SELECT full_name FROM {$this->tableName} WHERE student_id = ?";

			$stmt = $this->conn->prepare($query);

			$stmt->bindParam(1, $this->student_id);

			if (!$stmt->execute())
			{
				return FALSE;
			}

			$row = $stmt->fetech(PDO::FETCH_ASSOC);

			$this->fullName = $row['full_name'];

			return TRUE;
		}

		function getBulletin($tab)
		{
			//	Select bulletin data of given tab of specific student
			$query = "SELECT * FROM {$this->tableName} WHERE tab = :tab AND student_id = :student_id";

			//	Prepare query statement
			$stmt = $this->conn->prepare($query);

			//	Bind parameters
			$stmt->bindParam(':tab', $tab);
			$stmt->bindParam(':student_id', $this->student_id);

			//	Execute query
			if (!$stmt->execute())
			{
				return FALSE;
			}

			//	TODO check row count and show error if 0 row

			//	Get retrieved row
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			//	Set data to class member
			$this->data = $row['data'];

			//	Get data successful
			return TRUE;
		}

		//	Update hash only
		function updateHash($tab, $hash)
		{
			//	Update query
			$query = "UPDATE {$this->tableName} SET hash = :hash WHERE tab = :tab AND student_id = :student_id";

			//	Prepare query statement
			$stmt = $this->conn->prepare($query);

			//	Sanitize
			$student_id = htmlspecialchars(strip_tags($this->student_id));
			$tab = htmlspecialchars(strip_tags($tab));
			$hash = htmlspecialchars(strip_tags($hash));

			//	Bind new values
			$stmt->bindParam(':student_id', $student_id);
			$stmt->bindParam(':tab', $tab);
			$stmt->bindParam(':hash', $hash);

			//	Execute query
			if (!$stmt->execute())
			{
				return FALSE;
			}

			return TRUE;
		}

		//	Update table data and hash
		function updateTable($tab, $data, $hash)
		{
			//	Update data and hash
			$query = "UPDATE {$this->tableName} SET data = :data, hash = :hash WHERE tab = :tab AND student_id = :student_id";

			//	Prepare query statement
			$stmt = $this->conn->prepare($query);

			//	Sanitize
			$student_id = htmlspecialchars(strip_tags($this->student_id));
			$tab = htmlspecialchars(strip_tags($tab));
			$data = htmlspecialchars(strip_tags($data));
			$hash = htmlspecialchars(strip_tags($hash));

			//	Bind new values
			$stmt->bindParam(':student_id', $student_id);
			$stmt->bindParam(':tab', $tab);
			$stmt->bindParam(':data', $data);
			$stmt->bindParam(':hash', $hash);

			//	Execute query
			if (!$stmt->execute())
			{
				return FALSE;
			}

			return TRUE;

			//	TODO check error : $stmt->errorInfo();
		}

		function updateFullName($fullName)
		{
			$query = "UPDATE {$this->tableName} SET full_name = ? WHERE student_id = ?";

			$stmt = $this->conn->prepare($query);

			$stmt->bindParam(1, $fullName);
			$stmt->bindParam(2, $this->student_id);

			if (!$stmt->execute())
			{
				return FALSE;
			}

			return TRUE;
		}

		function insertNewUser()
		{
			$query = "SELECT * FROM {$this->tableName} WHERE student_id = ?";

			$stmt = $this->conn->prepare($query);

			$stmt->bindParams(1, $this->student_id);

			if (!$stmt->execute())
			{
				return FALSE;
			}

			//	Store result to get number of rows
			$stmt->store_result();

			//	If row for user existed
			if ($stmt->num_rows == 1)
			{
				return TRUE;
			}

			//	Row does not exist for user
			//	Create new row
			$query = "INSERT INTO {$this->tableName} (student_id) VALUES (?)";

			$stmt = $this->conn->prepare($query);

			$stmt->bindParams(1, $this->student_id);

			if (!$stmt->execute())
			{
				return FALSE;
			}

			return TRUE;
		}
	}