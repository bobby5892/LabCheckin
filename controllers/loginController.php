<?php

class LoginController{
	function __construct($config){
		$this->config = $config;
	}
	public function IsLoggedIn(){
		if(isset($_SESSION['LOGGEDIN'])){
			if($_SESSION['LOGGEDIN'] == "true"){
				// Pre-verify
				if(
					(isset($_SESSION['USER']) == false) || 
					(isset($_SESSION['USER']['emailaddress']) == false) || 
					(isset($_SESSION['USER']['passwordhash']) == false)
					){
					return false;
				}

				// Lets check password
				$q = new AdminQuery();
				$admin = AdminQuery::create()
				  ->filterByemailAddress(strtolower($_SESSION['USER']['emailaddress']))
				  ->limit(1)
				  ->find();
				// If the Session hashed password - matches the stored hash password
  				if($_SESSION['USER']['passwordhash'] == $admin[0]->getpasswordHash()){
					return true;
				}
			}
		}
		return false;
	}
	public function LoginForm(){
		return file_get_contents("../views/loginform.html");
	}
	public function Login(){
		// Check for a sanitized Valid Email 
		//https://emailregex.com/
		if (!(filter_var($_POST['emailAddress'], FILTER_VALIDATE_EMAIL))) {   
		     	return false;
		}
		// Check for Sanitized password
		if(preg_match("/^([0-9A-z].*?)$/",$_POST['password']) == false){
				return false; 
		}
		// See if a user exists with that email (all lowecase)
		try{
			$q = new AdminQuery();
			$admin = AdminQuery::create()
			  ->filterByemailAddress(strtolower($_POST['emailAddress']))
			  ->limit(1)
			  ->find();
			// No record found
			if($admin->count() != 1){ 
				print "<div class='Error'>Wrong Username or Password</div>";
				return false;
			}
			  // Check if password matches
			  	if($this->HashPass($_POST['password']) == $admin[0]->getpasswordHash()){
			  	// Update the Session
			  		$_SESSION['USER'] = array(
			  		"id" => $admin[0]->getId(),
			  		"name" => $admin[0]->getName(),
			  		"emailaddress" => $admin[0]->getemailAddress(),
			  		"passwordhash" => $admin[0]->getpasswordHash()
			  		);
			  		$_SESSION['LOGGEDIN'] = true;
			  		print "<script>self.location = self.location +'/..';</script>";
				}
				else{
					print "<div class='Error'>Wrong Username or Password</div>";
				}
				// user is logged in
				
			
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
			$newAdmin->setemailAddress(strtolower ($this->config['defaultAdminEmail']));
			$newAdmin->setpasswordHash($this->HashPass( $this->config['defaultAdminPassword']));
			$newAdmin->save();
			
		}	
	}
	public function LogOut(){
		$_SESSION['USER'] = "";
		print "<script>self.location = self.location +'/..';</script>";
	}	
	private function HashPass($plaintext){
		// Mcrypt deprecated in php 7.1 - so alternative below
		//https://coderwall.com/p/m2hkiw/php-encrypt-decrypt-generate-random-passwords-with-mcrypt
	/*	$td = mcrypt_module_open('cast-256', '', 'ecb', $string);
		$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		mcrypt_generic_init($td, $this->config['salt'], $iv);
		$encrypted_data = mcrypt_generic($td, $string);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		$encoded_64 = base64_encode($encrypted_data);
		return trim($encoded_64);
	*/
		//https://stackoverflow.com/questions/41272257/mcrypt-is-deprecated-what-is-the-alternative
		//$key should have been previously generated in a cryptographically safe way, like openssl_random_pseudo_bytes

		$cipher = "aes-128-gcm";
		if (in_array($cipher, openssl_get_cipher_methods())){
		    //$ivlen = openssl_cipher_iv_length($cipher);
		    //$iv = openssl_random_pseudo_bytes($ivlen);
		    $ciphertext = openssl_encrypt($plaintext, $cipher, $this->config['salt'], $options=0, $this->config['salt'], $tag);
		    //store $cipher, $iv, and $tag for decryption later
		    // $original_plaintext = openssl_decrypt($ciphertext, $cipher, $key, $options=0, $iv, $tag);
		    //echo $original_plaintext."\n";
		    return $ciphertext;
		}
	}
}