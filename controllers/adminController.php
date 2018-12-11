<?php
class AdminController{
	function __construct($config){
		$this->config = $config;
	}
	public function publicIndex(){
		return "Welcome admin";
	}
}