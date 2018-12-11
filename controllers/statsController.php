<?php


class StatsController {
	function __construct($config){
		$this->config = $config;
	}
	public function publicIndex(){
		return "Welcome";
	}
}