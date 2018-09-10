<?php
	class Users
	{
		//	Members for connection and table name
		private $conn;
		private $tableName = "users";
		
		//	Members for columns in the table "Users"
		public $id;
		public $full_name;
		public $student_id;
		public $email;
		public $password_mmuone;
		public $password_mmu;
		public $faculty;
		public $campus;
		public $profile_pic;
		public $last_login;
		public $date_registered;
		public $error;
		public $message = array();

		/**
		 * Users constructor. Get database connection and put in the private class member for connection
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

		/**
		 * This function will register new user by first checking the given email or user ID already exist
		 * If no email or user ID exist in the database, register the user and return true
		 *
		 * @return bool
		 */
		function create()
		{
			//	Check email & STUDENT ID before registering
			//	Select and check
			$query = "SELECT SUM(email = :email) AS emailCount, SUM(student_id = :student_id) AS studentIDCount FROM {$this->tableName} WHERE email = :email OR student_id = :student_id";
			
			//	Prepare query
			$stmt = $this->conn->prepare($query);
			
			//	Bind values
			$stmt->bindParam(":email", $this->email);
			$stmt->bindParam(":student_id", $this->student_id);
			
			//	Execute query
			$stmt->execute();

			//	Retrieve the data to $row
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			//	Find any duplication
			$row['allCount'] = $row['emailCount'] + $row['studentIDCount'];
			
			//	If duplication count is not 0
			if ($row['allCount'] != 0)
			{
				if ($row['allCount'] == 2)
				{
					//	Error: Duplicate entry for email and student ID
					$this->error = 10623;
				}
				else if ($row['emailCount'] == 1)
				{
					//	Error: Duplicate entry for email
					$this->error = 10621;
				}
				else if ($row['studentIDCount'] == 1)
				{
					//	Error: Duplicate entry for student ID
					$this->error = 10622;
				}

				//	Return false if duplication found
				return false;
			}
			
			//	No duplication found, proceed to register user:
			//	Insert record
			$query = "ALTER TABLE {$this->tableName} AUTO_INCREMENT = 1; INSERT INTO {$this->tableName} SET full_name = :full_name, email = :email, student_id = :student_id, password_mmuone = :password_mmuone, date_registered = :date_registered";
			
			//	Prepare query
			$stmt = $this->conn->prepare($query);
			
			//	Hash and encrypt password
			$this->password_mmuone = password_hash($this->password_mmuone, PASSWORD_DEFAULT);
			
			//	Bind values
			$stmt->bindParam(":full_name", $this->full_name);
			$stmt->bindParam(":email", $this->email);
			$stmt->bindParam(":student_id", $this->student_id);
			$stmt->bindParam(":password_mmuone", $this->password_mmuone);
			$stmt->bindParam(":date_registered", $this->date_registered);

			//	Execute query
			if ($stmt->execute())
			{
				//	If registration query succeeded, return true
				return true;
			}
			else
			{
				//	TODO GENERATE OWN ID
				//	Set error to be displayed
				$this->error = $stmt->errorInfo()[1];

				//	Return false if registration failed
				return false;
			}
		}

		/**
		 * This function will check the given user ID exist and check the password if the ID exists
		 *
		 * @return bool
		 */
		function loginUser()
		{
			//	Query to log in user
			$query= "SELECT full_name, password_mmuone, COUNT(id) as count FROM {$this->tableName} WHERE student_id = ?";
			
			//	Prepare query
			$stmt = $this->conn->prepare($query);
			
			//	Bind value
			$stmt->bindParam(1, $this->student_id);
			
			//	Execute query
			if (!$stmt->execute())
			{
				$this->error = $stmt->errorInfo[1];
				return false;
			}
			
			//	Get retrieved row
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			
			//	Set values to object variables
			$count = $row['count'];

			//	Check for errors
			if ($count > 1)
			{
				//	If user ID exists more than once
				$this->error = 10613;
			}
			else if ($count == 0)
			{
				//	If user ID doesn't exist
				$this->error = 10611;
			}
			else if ($count == 1)
			{
				//	If user ID exists and exist only once:
				//	Retrieve the full name
				$this->full_name = $row['full_name'];

				//	Verify the password encrypted with bcrypt using 'password_verify'
				if (password_verify($this->password_mmuone, $row['password_mmuone']))
				{
					//	Return true if password valid
					return true;
				}
				else
				{
					//	Wrong password
					$this->error = 10612;
				}
			}
			else
			{
				//	FATAL ERROR: Count is negative
				$this->error = 10614;
			}

			//	If any errors, return false
			return false;
			
			//	TODO update login time
			//	TODO change error text to codes
		}

		/**
		 * This function read one user give the user ID and store the data in :
		 * full_name, email, password_mmu, faculty, campus, profile_pic, last_login and date_registered
		 */
		function readOneByStudentID()
		{
			//	Query to read single record
			$query = "SELECT full_name, email, password_mmu, faculty, campus, profile_pic, last_login, date_registered FROM {$this->tableName} ORDER BY full_name WHERE student_id = ?";
			
			//	Prepare query
			$stmt = $this->conn->prepare($query);
			
			//	Bind value for student ID
			$stmt->bindParam(1, $this->student_id);

			//	Execute query
			if (!$stmt->execute())
			{
				$this->error = $stmt->errorInfo[1]; //$stmt->errorCode();
				return FALSE;
			}
			
			//	Get retrieved row
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			
			//	Set values to object properties
			$this->full_name = $row['full_name'];
			$this->email = $row['email'];
			$this->password_mmu = $row['password_mmu'];
			$this->faculty = $row['faculty'];
			$this->campus = $row['campus'];
			$this->profile_pic = $row['profile_pic'];
			$this->last_login = $row['last_login'];
			$this->date_registered = $row['date_registered'];

			return TRUE;
			//	TODO ERROR HANDLING
		}
		
		/**
		 * This function read all user data and return the array set in ascending order according to the full name
		 * Data included: full_name, student_id, email, password_mmu, faculty, campus, profile_pic, last_login, date_registered
		 */
		function read()
		{
			//	Select all queries
			$query = "SELECT full_name, student_id, email, password_mmu, faculty, campus, profile_pic, last_login, date_registered FROM {$this->tableName} ORDER BY full_name";
			
			//	Prepare query statement
			$stmt = $this->conn->prepare($query);
			
			//	Execute query
			$stmt->execute();

			//	Return array of data
			return $stmt;

			//	TODO ERROR HANDLING
		}

		/**
		 * This function will retrieve the MMU Password of a specific user and set the password at class member "password_mmu"
		 */
		function readPasswordMMU()
		{
			//	Select password_mmu
			$query = "SELECT password_mmu FROM {$this->tableName} WHERE student_id = ?";
			
			//	Prepare query statement
			$stmt = $this->conn->prepare($query);
			
			//	Bind value for student ID
			$stmt->bindParam(1, $this->student_id);
			
			//	Execute query
			$stmt->execute();
			
			//	Get retrieved row
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			//	Get password from row
			$this->password_mmu = $row['password_mmu'];

			//	TODO ERROR HANDLING
		}

		/**
		 * This function converts a given error code or if no parameter given, retrieve the error code from class member
		 * and convert it to its error text and return it
		 *
		 * @param null $errorCode
		 * @return string
		 */
		function getErrorText($errorCode = NULL)
		{
			//	Error text variable
			$errorText = NULL;

			if (is_null($errorCode))
			{
				$errorCode = $this->error;
			}
			
			//	TODO ADD MORE ERROR CODES
			//	Convert error code to text
			switch ($errorCode)
			{
				//	Read One function:
				case '10600':
					$errorText = "FATAL ERROR: No Student ID received";
					break;
				//	Logging In:
				case '10611':
					$errorText = "NO ACCOUNT FOUND";
					break;
				case '10612':
					$errorText = "PASSWORD ERROR";
					break;
				case '10613':
					$errorText = "FATAL ERROR: NON UNIQUE ID WHILE SIGNING IN";
					break;
				case '10614':
					$errorText = "FATAL ERROR: COUNT IS NEGATIVE WHILE SIGNING IN";
					break;
				//	Registration:
				case '10621':
					$errorText = "DUPLICATE ENTRY FOR EMAIL";
					break;
				case '10622':
					$errorText = "DUPLICATE ENTRY FOR STUDENT ID";
					break;
				case '10623':
					$errorText = "DUPLICATE ENTRY FOR EMAIL AND STUDENT ID";
					break;
				//	Error code given not known:
				default:
					$errorText = "Unknown error occured";					
			}

			//	Return the converted error text to be displayed
			return $errorText;
		}

		/**
		 * This function will retrieve message from class member with keys and values
		 * Then this function will output JSON reply with curly braces and comma(s)
		 */
		function echoMessage()
		{
			//	Echo the opening { for JSON output
			echo "{";

			//	Get last element key
			end($this->message);
			$lastElementKey = key($this->message);

			//	Coverting array's keys and values to JSON with comma
			foreach ($this->message as $key => $value)
			{
				//	Echo the key and value
				echo "\"{$key}\": \"{$value}\"";

				//	If not end of message, echo comma (,)
				if ($key != $lastElementKey)
				{
					echo ",";
				}	
			}

			//	Echo the closing } for JSON output
			echo "}";			
		}
	}