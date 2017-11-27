<?php
	class Portal
	{
		private $htmlDOM;
		private $student_id;
		private $password_mmu;
		private $fullName;
		private $bulletin1;
		private $bulletin3;
		private $bulletin2;
		private $bulletinPaged;
		private $bulletinRetrievalCount1 = 1;
		private $bulletinRetrievalCount2 = 1;
		private $bulletinRetrievalCount3 = 1;
		private $bulletinSize1;
		private $bulletinSize2;
		private $bulletinSize3;
		private $curl;
		private $postRequest;
		private $data = array();
		private $curlResult;
		private $latestTitleOnAndroid;
		private $loggedIn = false;
		private $url;
		private $cookie;
		private $error;
		private $message = array();
		
		//	Constructor
		public function __construct($htmlDOM)
		{
			$this->htmlDOM = $htmlDOM;
			
			$this->cookie = tempnam("/cookie", "PORTAL_");	//unlink($cookie);
			
			//	Include cURL function: curl(url, postRequest, data, cookie)
			require_once '../objects/curl.php';
		}
		
		//	Destructor
		public function __destruct()
		{
			//	Close cURL resource and free up system resources
			if (!is_null($this->curl))
			{
				curl_close($this->curl);	
			}
		}
		
		function login($studentID, $passwordMMU)
		{
			//	Login user
			//	Session ends when browser ends
			//	Get student ID and MMU password
			$this->student_id = $studentID;
			$this->password_mmu = $passwordMMU;
			
			//	URL of MMU Portal login
			$this->url = "https://online.mmu.edu.my/index.php";
			
			//	Data for Login POST
			$data = array('form_loginUsername' => $this->student_id, 'form_loginPassword' => $this->password_mmu);
			
			//	It is a POST request
			$this->postRequest = true;
			
			$this->curlResult = curl($this->curl, $this->url, $this->postRequest, $data, $this->cookie);
			
			if ($this->curlResult[0] == "failed")
			{
				//	log in failed
				$this->error = 20600;
				
				return false;
			}
			else if ($this->curlResult[0] == "succeed")
			{
				$this->loggedIn = true;
				return true;
			}
		}
		
		function getFullName()
		{
			if (!$this->loggedIn)
			{
				//	Not logged in
				$this->error = 20601;
				
				return false;
			}
			
			if (!empty($this->fullName))
			{
				return $this->fullName;
			}
			
			//	Check for id "headerWrapper" that will contain "Welcome, (Full Name)"
			//	Load the string to HTML DOM without stripping /r/n tags
			$this->htmlDOM->load($this->curlResult[1], true, false);
			
			//	Find the desired input field
			$inputFullName = $this->htmlDOM->find('#headerWrapper .floatL');
			
			//	Get the full name by filtering text at the front and back
			$this->fullName = trim(substr(trim($inputFullName[0]->plaintext), 8, strripos($inputFullName[0]->plaintext, "(") - 8));
			
			return $this->fullName;
		}
		
		function getBulletin()
		{
			//	If bulletin1 is not empty means already retrieved before
			if (!empty($this->bulletin1))
			{
				//	Proccess the bulletin to return the next page of bulletin
				//	Check if bulletin is less than 10 means previously already returned all news
				if ($this->bulletinSize1 < $this->bulletinRetrievalCount1 * 10)
				{
					$bulletinPaged1 = array();
				}
				else
				{
					foreach ($this->bulletin1 as $key => $bulletinSingle)
					{
						array_push($bulletinPaged1, $bulletinSingle->plaintext);
						array_splice($bulletinPaged1, 0, 1);
						if ($key = 9)
						{
							break;
						}
					}
					$this->bulletinRetrievalCount1++;
				}
				
				if ($this->bulletinSize2 < $this->bulletinRetrievalCount2 * 10)
				{
					$bulletinPaged2 = array();
				}
				else
				{
					foreach ($this->bulletin2 as $key => $bulletinSingle)
					{
						array_push($bulletinPaged2, $bulletinSingle->plaintext);
						array_splice($bulletinPaged2, 0, 1);
						if ($key = 9)
						{
							break;
						}
					}
					$this->bulletinRetrievalCount2++;
				}
				
				if ($this->bulletinSize3 < $this->bulletinRetrievalCount3 * 10)
				{
					$bulletinPaged3 = array();
				}
				else
				{
					foreach ($this->bulletin3 as $key => $bulletinSingle)
					{
						array_push($bulletinPaged3, $bulletinSingle->plaintext);
						array_splice($bulletinPaged3, 0, 1);
						if ($key = 9)
						{
							break;
						}
					}
					$this->bulletinRetrievalCount3++;
				}
				
				//	Combine all bulletin tabs together as a multidimensional array
				$this->bulletinPaged = array($bulletinPaged1, $bulletinPaged2, $bulletinPaged3);
				
				return $this->bulletinPaged;
			}
			
			//	Get all the bulletin news
			//	URL of MMU Portal's Bulletion Boarx
			$this->url = "https://online.mmu.edu.my/bulletin.php";
			
			$this->postRequest = false;
			
			$this->curlResult = curl($this->curl, $this->url, $this->postRequest, $this->data, $this->cookie);
			
			if ($this->curlResult[0] == "failed")
			{
				$this->error = $this->curlResult[1];
				
				//	Get bulletin failed
				$this->error = 20602;
				
				return false;
			}
			else if ($this->curlResult[0] == "succeed")
			{
				//	Load the string to HTML DOM without stripping /r/n tags
				$this->htmlDOM->load($this->curlResult[1], true, false);
				
				//	Find the desired input field
				$this->bulletin1 = $this->htmlDOM->find('div[id=tabs-1] div.bulletinContentAll');
				$this->bulletin2 = $this->htmlDOM->find('div[id=tabs-2] div.bulletinContentAll');
				$this->bulletin3 = $this->htmlDOM->find('div[id=tabs-3] div.bulletinContentAll');

				//	Count array size
				$this->bulletinSize1 = count($this->bulletin1);
				$this->bulletinSize2 = count($this->bulletin2);
				$this->bulletinSize3 = count($this->bulletin3);

				//	Send the bulletin 10 by 10
				$bulletinPaged1 = array();
				foreach ($this->bulletin1 as $key => $bulletinSingle)
				{
					array_push($bulletinPaged1, $bulletinSingle->plaintext);
//					array_splice($this->bulletin1, 0, 1);
					if ($key == 9)
					{
						break;
					}
				}
			
				$bulletinPaged2 = array();
				foreach ($this->bulletin2 as $key => $bulletinSingle)
				{
					array_push($bulletinPaged2, $bulletinSingle->plaintext);
					array_splice($this->bulletin2, 0, 1);
					if ($key == 9)
					{
						break;
					}
				}
			
				$bulletinPaged3 = array();
				foreach ($this->bulletin3 as $key => $bulletinSingle)
				{
					array_push($bulletinPaged3, $bulletinSingle->plaintext);
					array_splice($this->bulletin3, 0, 1);
					if ($key == 9)
					{
						break;
					}
				}
				
				//	Combine all bulletin tabs together as a multidimensional array
				$this->bulletinPaged = array($bulletinPaged1, $bulletinPaged2, $bulletinPaged3);
					
				return $this->bulletinPaged;
			}
		}
		
		function getErrorText()
		{
			
		}
		
		function echoMessage()
		{
			
		}
	}