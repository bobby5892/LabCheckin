<?php
class Response{
	protected $response = "";
	protected $isSuccess = false;
	public function __construct($isSuccess=false,$response="No Data"){
		$this->isSuccess = $isSuccess;
		$this->response = $response;
	}

	public function GetResponse(){
		return $this->response;
	}
	public function SetResponse($response){
		$this->response = $response;
	}
	public function IsSuccess(){
		return $this->isSuccess;
	}
	public function SetSuccess($success){
		$this->isSuccess = $success;
	}
	public function ToJSON(){
		$output = new \stdClass();
		$output->success = $this->isSuccess;
		$output->response = $this->response;
		$output = json_encode($output,JSON_PRETTY_PRINT);
		return $output;
	}
}