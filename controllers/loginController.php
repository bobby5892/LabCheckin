<?php

class LoginController{
	function __construct($config){
		$this->config = $config;
	}
	public function IsLoggedIn(){
		if(isset($_SESSION['LoggedIn'])){
			if($_SESSION['LoggedIn'] == "true"){
				return true;
			}
		}
		return false;
	}
	public function LoginForm(){
		return require("../views/loginform.php");
	}
	public function Login(){
		// Check for a sanitized Valid Email 
		if(preg_match("/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/",$_POST['emailAddress'],$matches)) {
		     if(count($matches) != 1){ return false;}
		}
		// Check for Sanitized password
		if(preg_match("/^([0-9A-z].*?)$/",$_POST['password'],$matches)){
			if(count($matches) != 1) { return false; }
		}
		
		// See if a user exists with that email (all lowecase)
		try{

		}
		catch(Exception $e){
			return false;
		}
		// See if the hash password matches
		
		
		return false;
	}
	public function AddDefaultIfEmpty(){
		// Check if there are no users
		// Add the default

		if($this->config['debugMode'] == true){
			//Propel::getConnection()->useDebug(true);
		}
		$q = new AdminQuery();
		$firstAdmin = $q->findPK(1);
		if($firstAdmin == ""){
			// Doesn't exist - lets create one
			$newAdmin = new Admin();
			$newAdmin->setName("Admin");
			$newAdmin->setemailAddress($this->config['defaultAdminEmail']);
			$newAdmin->setpasswordHash($this->HashPass( $this->config['defaultAdminPassword']));
			$newAdmin->save();
		}	
	}
	private function HashPass($string){
		//https://coderwall.com/p/m2hkiw/php-encrypt-decrypt-generate-random-passwords-with-mcrypt
		$td = mcrypt_module_open('cast-256', '', 'ecb', $string);
		$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		mcrypt_generic_init($td, $this->config['salt'], $iv);
		$encrypted_data = mcrypt_generic($td, $string);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		$encoded_64 = base64_encode($encrypted_data);
		return trim($encoded_64);
	}
}