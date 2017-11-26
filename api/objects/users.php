<?php
	class Users
	{
		private $conn;
		private $tableName = "users";
		
		//	Columns
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
		
		//	Constructor
		public function __construct($db)
		{
			$this->conn = $db;
		}
		
		//	Destructor
		public function __destruct()
		{
			
		}
		
		//	Create user (REGISTER)
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
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$row['allCount'] = $row['emailCount'] + $row['studentIDCount'];
			
			//	Find any duplication
			if ($row['allCount'] != 0)
			{
				if ($row['allCount'] == 2)
				{
					//	Error 10623
					$this->error = 10623;
				}
				else if ($row['emailCount'] == 1)
				{
					//	Error 10621
					$this->error = 10621;
				}
				else if ($row['studentIDCount'] == 1)
				{
					//	Error 10622
					$this->error = 10622;
				}
				return false;
			}
			
			//	Register user
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
				return true;
			}
			else
			{
				$this->error = $stmt->errorInfo()[1];
				return false;
			}
		}
		
		//	Login user 
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
			
			if ($count > 1)
			{
				$this->error = 10613;
			}
			else if ($count == 0)
			{
				$this->error = 10611;
			}
			else if ($count == 1)
			{
				$this->full_name = $row['full_name'];
				
				if (password_verify($this->password_mmuone, $row['password_mmuone']))
				{
					return true;
				}
				else
				{
					$this->error = 10612;
				}
			}
			else
			{
				$this->error = 10614;
			}
			
			return false;
			
			//	TODO update login time
			//	TODO change error text to codes
		}
		
		//	Read one user
		function readOneByStudentID()
		{
			//	Query to read single record
			$query = "SELECT full_name, email, password_mmu, faculty, campus, profile_pic, last_login, date_registered FROM {$this->tableName} ORDER BY full_name WHERE student_id = ?";
			
			//	Prepare query
			$stmt = $this->conn->prepare($query);
			
			//	Bind value for student ID
			$stmt->bindParam(1, $this->student_id);
			
			//	Execute query
			$stmt->execute();
			
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
		}
		
		//	Read all users
		function read()
		{
			//	Select all queries
			$query = "SELECT full_name, student_id, email, password_mmu, faculty, campus, profile_pic, last_login, date_registered FROM {$this->tableName} ORDER BY full_name";
			
			//	Prepare query statement
			$stmt = $this->conn->prepare($query);
			
			//	Execute query
			$stmt->execute();
			
			return $stmt;
		}
		
		function readPasswordMMU()
		{
			//	Select password_mmu
			$query = "SELECT password_mmu FROM {$this->tableName} WHERE student_id = ?";
			
			//	Prepare query statement
			$stmt = $this->conn->prepare($query);
			
			//	Bind value for student ID
			$stmt->bindParam(1, $this->student_id);
			
			//	Execute query
			//$stmt->execute();
			//	Execute query
			if (!$stmt->execute())
			{
				$this->password_mmu = $stmt->errorInfo[1];
				return;
			}
			
			//	Get retrieved row
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			
			//$this->password_mmu = $row['password_mmu'];
		}
		
		function getErrorText($errorCode = NULL)
		{
			//	Error text variable
			$errorText;
			if (is_null($errorCode))
			{
				$errorCode = $this->error;
			}
			
			//	TODO
			//	Convert error code to text
			switch ($errorCode)
			{
				//	Read One:
				case '10600':
					$errorText = "FATAL ERROR: No Student ID received";
					break;
				//	Signing In:
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
				//	No error code:
				default:
					$errorText = "Unknown error occured";					
			}
			
			return $errorText;
		}
		
		function echoMessage()
		{
			$msgCheck = $this->message;
			echo "{";
			foreach ($this->message as $key => $value)
			{
				echo "\"{$key}\": \"{$value}\"";
				if (end($msgCheck) != $value)
				{
					echo ",";
				}	
			}
			echo "}";			
		}
	}