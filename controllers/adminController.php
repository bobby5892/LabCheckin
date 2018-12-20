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

class AdminController{
	function __construct($config){
		$this->config = $config;
		$this->template = new TemplateController($config);
	}
	public function publicIndex(){	
		$content = "";
		// current lab usage widget
	    $content .= $this->template->partialView("admincurrentlab.html");
		return $this->template->adminContentView($content);
	}
/* Classes */	
	public function editCourses(){
		$content = "";
		// Navbar
		$content .= $this->template->partialView("coursenav.html");

		if((isset($_POST['save'])) && ($_POST['save'] == true)){
			// Add a course
			$course = new Course();
			$course->setName($_POST['name']);

			if($course->validate()){
				$response = new Response(
					"true",
					"Successfully Added"
				);
			}
			else{
				$validationError = "";
				foreach($course->getValidationFailures() as $failure){
					$validationError .="<BR>". $failure->getMessage();
				}
				$response = new Response(
					$course->validate(),
					$validationError
				);	
			}
			
			// Check for duplicates
			$courses = CourseQuery::create()
				->filterByName($_POST['name'])
				->limit(1)
				->count();
			$IsDupe = true;

			if($courses == 0){
				$IsDupe = false;
			}
			else{
				$response->SetResponse("Already exists - ignored");
				$response->SetSuccess(false);
			}

			// If its a valid course name
			if($course->validate() && ($IsDupe == false)){
				$course->save();
			}
			$response->ToJSON();
		

		}
		else if((isset($_GET['section'])) && ($_GET['section'] == "add")){
			$content .= $this->template->partialView("courseaddform.html");
		}
		else if((isset($_GET['section'])) && ($_GET['section'] == "data")){
			// New array
			$output =[];
			$output['data'] = [];
			// query courses
			$courses = CourseQuery::create()->find();
			// stack in array
			foreach($courses as $course) {
			
			  array_push($output['data'], array((string) $course->GetName()));
			}
			print json_encode($output);
			exit;
		}
		else{
			$content.= "
			<script>
			$(document).ready( function () {
			    $('#myCourseList').DataTable({
			    	 ajax: '" . $this->config["basePrefix"] ."/admin/editcourses?section=data'
			    }
			 )});
			</script>";
			// show coures
			$content .= $this->template->partialView("courselist.html");
		}

		return $this->template->adminContentView($content);
	}

/* Users */	
	public function editUsers(){	
		$content = "edit users";
		
		return $this->template->adminContentView($content);
	}

	public function getCourses(){
		$courses = CourseQuery::create()
		->find();

		$output =  array(
			'data' => array()
		);
	
		foreach($courses as $course){
			$temp = array(
			'id' => $course->getId(),
			'name' => $course->getName()
			);

			array_push($output['data'],$temp);
		}
		return json_encode($output);
	}
	public function getLiveLab(){
		// get checkins within the last 12 hrs
		$labVisits = LabVisitQuery::create()
		->filterByCheckin(array('min' => strtotime("-12 hour")))
		->find();
		$output =  array(
			'data' => array()
		);
	
		foreach($labVisits as $labVisit){
			// skip checkins that are already out
			if(is_null($labVisit->getCheckout())){
				//https://stackoverflow.com/questions/365191/how-to-get-time-difference-in-minutes-in-php
				$time = $labVisit->getCheckin()->getTimestamp();
				$start_date = new DateTime();
				$start_date->setTimeStamp($time);
				$since_start = $start_date->diff(new DateTime("now"));
				$hrs = $since_start->h;
				$min = $since_start->i;
				$duration = "";
				if($hrs > 1){
					$duration .= $hrs . " hours ";
				}
				else if ($hrs == 1){
					$duration .= $hrs . " hour ";
				}
				if($min <= 1){
					$duration .= $min . " minute";
				}
				else{
					$duration .= $min . " minutes";
				}

				
				$temp = array(
				'studentid' => $labVisit->getStudentid(),
				'courseid' => $labVisit->getCourseid(),
				'checkin' => $labVisit->getCheckin(),
				'duration' => $duration
				);
				array_push($output['data'],$temp);
			}
		}
		return json_encode($output);
	}
/* Search */	
	public function Search(){	
		$content = "Not implemented";
		
		return $this->template->adminContentView($content);
	}

}