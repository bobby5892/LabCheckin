<?php
class AdminController{
	function __construct($config){
		$this->config = $config;
	}
	public function publicIndex(){	
		$content = "things";
		
		return $this->contentView($content);
	}
	public function editClasses(){	
		$content = "edit classes";
		
		return $this->contentView($content);
	}
	public function editUsers(){	
		$content = "edit users";
		
		return $this->contentView($content);
	}
	public function reportsByClass(){	
		$content = "reportsByClass";
		
		return $this->contentView($content);
	}
	public function reportsByPeriod(){	
		$content = "reportsByPeriod";
		
		return $this->contentView($content);
	}
	public function Search(){	
		$content = "search";
		
		return $this->contentView($content);
	}
	private function contentView($newcontent){
		// Replace absolute path for a relative path
		$header = file_get_contents("../views/adminheader.html");
		$header = str_replace('{base}', $this->config["basePrefix"],$header);
		$content = $header;
		// Replace absolute path for a relative path
		$nav = file_get_contents("../views/adminnav.html");
		$nav = str_replace('{base}', $this->config["basePrefix"],$nav);
		$content .= $nav;
		// Add Welcome USer
		$welcome = file_get_contents("../views/adminwelcome.html");
		$welcome  = str_replace('{content}', "Current User: " . $_SESSION['USER']["name"], $welcome); 
		$content .= $welcome;
		// Add the content from method
		$content .= $newcontent;
		$content .= file_get_contents("../views/adminfooter.html");
		return $content;
	}
}