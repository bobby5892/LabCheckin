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

require("../models/Response.php");
require('../controllers/templateController.php');
class CheckinController{
	function __construct($config){
		$this->config = $config;
		$this->template = new TemplateController($config);
	}
	public function Index(){	
		$content = $this->template->partialView("checkinform.html");
		return $this->template->publicContentView($content);
	}
	public function ValidateL(){
		$response = new Response(false,"Successfully Validated");
		if(isset($_POST['studentid'])){
			$labVisit = new LabVisit();
			$labVisit->setStudentid($_POST['studentid']);

			if($labVisit->validate()){
				$response = new Response("true","Successfully Validated");
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
		
		}
		return $response->ToJSON();

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
		return json_encode($courseList);
	}
	public function SaveCheckOut(){
		$checkins = $this->getCheckinsForStudent($_POST['studentid']);
		$found = false;
		foreach ($checkins as $checkin){
			// If its not been checked out but it is checked in
			if(is_null($checkin->getCheckout()) && $this->checkedIn($checkin)){
				$checkin->setCheckout(new DateTime("now"));
				$checkin->save();
				$response = new Response(true, "Marked as checked out");
			
				$found = true;
			}
		}
		if(!$found){
			$response = new Response(false, "Did not find checkin");
			
		}
		return $response->ToJSON();
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
					$response->ToJSON();
					
				}
				// Not a dupe lets save it
				$response = new Response($labVisit->validate(),"Saved checkin");

				$labVisit->save();
				return $response->ToJSON();
			}
			else{
				$validationError = "";
				foreach($labVisit->getValidationFailures() as $failure){
					$validationError .="<BR>". $failure->getMessage();
				}
				$response = new Response($labVisit->validate(),$validationError);
				return $response->ToJSON();
			}
		}
		catch(Exception $e){
				$response = new Response("false","Invalid course ID");
		}

		
		
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
}