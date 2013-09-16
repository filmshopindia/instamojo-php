<?php

class Instamojo{
	
	// internal constant to enable/disable debugging.
	const debug = true;
	
	// Instamojo API base URL.
	const API_URL = 'https://www.instamojo.com/api/1/';

	// API version.
	const version = '0.0';

	// A curl instance.
	protected $curl;

	// The username of the User.
	protected $username = null;

	// The password of the User
	protected $password = null; 

	// Token provided by Instamojo.
	protected $APP_TOKEN = null;

	//  APP_ID provided by Instamojo.
	protected $APP_ID = null;

	// Time-out.
	protected $timeout = 10;


	/**
		* Default constructor
		* @param string $username Instamojo username of the user.
		* @param string $password Instamojo password of the user.
	*/
	public function __construct($username, $password, $id){
		$this->setUsername($username);
		$this->setPassword($password);
		$this->setID($id);
	}

	/**
		* Default destructor.
	*/
	public function __destruct(){
		if($this->curl != null) curl_close($this->curl);
	}

	/**
		* Set teh Username.
		* @param string $username Instamojo username of the user.
	*/
	private function setUsername($username){
		$this->username = (string) $username;
	}

	/**
		* Set the password.
		* @param string $password Instamojo username of the password.
	*/
	private function setPassword($password){
		$this->password = (string) $password;
	}

	/**
		* Set the ID.
		* @param string $id Instamojo APP_ID provided by Instamojo.
	*/

	private function setID($id){
		$this->APP_ID = (string) $id;
	}

	/**
		* Create the absolute path for the request.
		* @param string $url The base URL (Here it is used by API_URL)
		* @param string $path The relative path.
	*/
	private function buildPath($url, $path){
		return $url . $path;
	}

	/**
		* Request the instamojo API.
		* @param string $path The relative path.
		* @param string $method POST/GET/POST/DELETE
		* @param array $data Data to be passed. 
	*/
	private function apiRequest($path, $method, array $data = null){
		$path = (string) $path;
		$method = (string) $method;
		$data = (array) $data;

		$headers = array("X-App-Id: $this->APP_ID");

		if($this->APP_TOKEN){
			$headers[] = "X-Auth-Token: $this->APP_TOKEN";
		}

		$request_path = $this->buildPath(self::API_URL, $path);

		$options = array();
		$options[CURLOPT_HTTPHEADER] = $headers;
		$options[CURLOPT_RETURNTRANSFER] = true;

		if($method == 'POST'){
			$data_string = "";

			foreach ($data as $key => $value) {
				$data_string .= $key.'='.$value.'&';
			}

			$data_string = rtrim($data_string, '&');

			$options[CURLOPT_URL] = $request_path;
			$options[CURLOPT_POST] = count($data);
			$options[CURLOPT_POSTFIELDS] = $data_string;
		}

		else if($method == 'GET'){

		}

		else if($method == 'PUT'){

		}

		else if($method == 'DELETE'){

		}

		$this->curl = curl_init();
		curl_setopt_array($this->curl, $options);

		$response = curl_exec($this->curl);
		$headers = curl_getinfo($this->curl);

		$errorNumber = curl_errno($this->curl);
		$errorMessage = curl_error($this->curl);

		return array('response' => $response, 'headers' => $headers, 'error' => $errorMessage, 'errno' => $errorNumber);
	}


	/**
		* Authenticate the application.
	*/
	public function apiAuth(){
		$response = $this->apiRequest('auth/', 'POST', $data = array('username' => $this->username, 'password' => $this->password));
		$json = @json_decode($response['response'], true);
		if($response['errno']) throw new Exception("Exception: " . $response['error']);
		return $json;
	}
}

$instance = new Instamojo('username', 'password', 'app_id');
$temp = $instance->apiAuth();
print_r($temp);
?>