<?php
/*
[---------------------------------------------------]

   __       _         ___ _               _    
  / /  __ _| |__     / __\ |__   ___  ___| | __
 / /  / _` | '_ \   / /  | '_ \ / _ \/ __| |/ /
/ /__| (_| | |_) | / /___| | | |  __/ (__|   < 
\____/\__,_|_.__/  \____/|_| |_|\___|\___|_|\_\

[---------------------------------------------------]                                               

Lab Check
by Robert Moore 12/19/2018

robert@eugeneprogramming.com
https://github.com/bobby5892/LabCheckin

License CC BY 
https://creativecommons.org/licenses/by/4.0/
*/
class TemplateController {
	function __construct($config){
		$this->config = $config;
	}
	public function partialView($filename){
		$content = file_get_contents("../views/" . $filename);
		$content = str_replace('{base}', $this->config["basePrefix"],$content);
		return $content;
	}
	/* This is the common view for admin users */
	public function adminContentView($newcontent){
		$header = $this->partialView("adminheader.html");
		$content = $header;
		$nav = $this->partialView("adminnav.html");
		$content .= $nav;
		// Add Welcome USer
		$welcome = $this->partialView("adminwelcome.html");
		$welcome  = str_replace('{content}', "Current User: " . $_SESSION['USER']["name"], $welcome); 
		$content .= $welcome;
		// Add the content from method
		$content .=$newcontent;
		$content .= $this->partialView("adminfooter.html");
		return $content;
	}
	/* This is the common view for non-logged in users */
	public function publicContentView($newcontent){
		$header = file_get_contents("../views/checkinheader.html");
		$header = str_replace('{base}', $this->config["basePrefix"],$header);
		$content = $header;
		$nav = file_get_contents("../views/checkinnav.html");
		$nav = str_replace('{base}', $this->config["basePrefix"],$nav);
		$content .= $nav;
		// Add the content from method
		$content .= $newcontent;
		$content .= file_get_contents("../views/checkinfooter.html");
		return $content;
	}	
	
}