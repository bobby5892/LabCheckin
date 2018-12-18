<?php
require("../models/Response.php");
class CheckinController{
	function __construct($config){
		$this->config = $config;
	}
	public function Index(){	
		$content = $this->partialView("checkinform.html");
		return $this->contentView($content);
	}
	public function ValidateL(){
		if(isset($_POST['studentid'])){
			$labVisit = new LabVisit();
			$labVisit->setStudentid($_POST['studentid']);

			if($labVisit->validate()){
				$response = new Response(
					"true",
					"Successfully Validated"
				);
			}
			else{
				$validationError = "";
				foreach($labVisit->getValidationFailures() as $failure){
					$validationError .="<BR>". $failure->getMessage();
				}

				$response = new Response(
					$labVisit->validate(),
					$validationError
				);	
			}
			print $response->ToJSON();
		}
		exit;

	}
	public function IsCheckedIn(){
		if(isset($_POST['studentid'])){
			$labVisit = new LabVisit();
			$labVisit->setStudentid($_POST['studentid']);
			
			$response = new Response($this->checkedIn($labVisit), "Student is already checked in");
			return $response->ToJSON();
		}
		$response = new Response(false, "Student is not checked in");
		return $response->ToJSON();
	}
	public function GetCourses(){
		$courseList = [];
		$courses = CourseQuery::create()->find();
		foreach ($courses as $course){
			$temp = array(
				"id" => $course->getId(),
				"name"=> $course->getName()
			);
			array_push($courseList,$temp);
		}
		print json_encode($courseList);
		exit;
	}
	// Save Checkout/Checkin
	public function SaveCheck(){
		$labVisit = new LabVisit();
		$labVisit->setStudentid($_POST['studentid']);
		$labVisit->setCheckin(new DateTime("now"));
		$labVisit->setCourseid($_POST['courseid']);

		$response = "";
		try{
			if($labVisit->validate()){
				if($this->checkedIn($labVisit)){
					$response = new Response(false,"Already checked in");	
					print $response->ToJSON();
					exit;
				}
				// Not a dupe lets save it
				$response = new Response($labVisit->validate(),"Saved checkin");
			}
			else{
				$validationError = "";
				foreach($labVisit->getValidationFailures() as $failure){
					$validationError .="<BR>". $failure->getMessage();
				}
				$response = new Response($labVisit->validate(),$validationError);
			}
		}
		catch(Exception $e){
				$response = new Response("false","Invalid course ID");
		}

		print $response->ToJSON();
		exit;
	}
	private function checkedIn($labVisit){
		
		// check for dupes
				$dateMin = new DateTime();
				$toMin = new DateInterval('PT12H');
				$dateMin->sub($toMin);

				$currentTime = new DateTime("now");

				$checkins = LabVisitQuery::create()
				->filterByCheckin(array("min" => $dateMin, "max" => $currentTime))
				->filterByStudentid($labVisit->getStudentid())
				->find();

				foreach($checkins as $checkin){
					if($checkin->getCheckout() == ""){
						return true;
					}
				}
				return false;
	}
	private function contentView($newcontent){
		// Replace absolute path for a relative path
		$header = file_get_contents("../views/checkinheader.html");
		$header = str_replace('{base}', $this->config["basePrefix"],$header);
		$content = $header;
		// Replace absolute path for a relative path
		$nav = file_get_contents("../views/checkinnav.html");
		$nav = str_replace('{base}', $this->config["basePrefix"],$nav);
		$content .= $nav;
		// Add the content from method
		$content .= $newcontent;
		$content .= file_get_contents("../views/checkinfooter.html");
		return $content;
	}
	private function partialView($filename){
		$content = file_get_contents("../views/" . $filename);
		$content = str_replace('{base}', $this->config["basePrefix"],$content);
		return $content;
	}
}