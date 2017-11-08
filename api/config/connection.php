<?php
	class Database
	{
		private $conn;

		public function connect()
		{
			require_once '../../../config.php';
			$dsn = "mysql:dbname={$DB_DATABASE};host={$DB_HOST};charset=utf8";

			try
			{
				$this->conn = new PDO($dsn, $DB_USER, $DB_PASSWORD);
			}
			catch (PDOException $e)
			{
				echo "Connection failed: " . $e->getMessage();
			}

			return $this->conn;
		}
	}
