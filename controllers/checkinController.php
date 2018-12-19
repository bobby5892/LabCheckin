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
			
			$boolresponse = $this->checkedIn($labVisit);
			if($boolresponse){
				$response = new Response(true, "Student is checked in");		
			}
			else{
				$response = new Response(false, "Student is not checked in");
			}
			
		}
		else{
			$response = new Response(false, "No student id sent");
		}
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
	public function SaveCheckOut(){
		$checkins = $this->getCheckinsForStudent($_POST['studentid']);

		foreach ($checkins as $checkin){
			// If its not been checked out but it is checked in
			if(is_null($checkin->getCheckout()) && $this->checkedIn($checkin)){
				$checkin->setCheckout(new DateTime("now"));
				$checkin->save();
				return (new Response(true, "Marked as checked out"))->ToJSON();
				
				exit;
			}
		}
		return (new Response(false, "Did not find checkin"))->ToJSON();
	}
	// Save Checkin
	public function SaveCheckIn(){
		$labVisit = new LabVisit();
		$labVisit->setStudentid($_POST['studentid']);
		$labVisit->setCheckin(new DateTime("now"));
		$labVisit->setCourseid($_POST['courseid']);

		$response = "";
		try{
			if($labVisit->validate()){
				// If they are checked in and not checked out
				if($this->checkedIn($labVisit)){
					$response = new Response(false,"Already checked in");	
					print $response->ToJSON();
					exit;
				}
				// Not a dupe lets save it
				$response = new Response($labVisit->validate(),"Saved checkin");

				$labVisit->save();
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
	// Public so accessible for testing
	public function checkedIn($labVisit){
				$studentid = $labVisit->getStudentid();
				
				$checkins = LabVisitQuery::create()
				->filterByStudentid($studentid)
				->find();

				foreach($checkins as $checkin){
					$checkinTime = strtotime((string)$checkin->getCheckin()->getTimestamp());
					$checkinTimeTwelveAgo = strtotime("-12 hours");
					if($checkinTime <= $checkinTimeTwelveAgo){
						//print "checkin is less than 12 hrs";
						if(is_null($checkin->getCheckout())){
							return true;
						}
					}
				}
				return false;
	}
	private function getCheckinsForStudent($studentid){
		$labVisits = LabVisitQuery::create()
		->filterByStudentid($studentid)
		->find();
		return $labVisits;
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