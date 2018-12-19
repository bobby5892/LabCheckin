<?php
require("../models/Response.php");
class AdminController{
	function __construct($config){
		$this->config = $config;
	}
	public function publicIndex(){	
		$content = "";
		// current lab usage widget
	    $content .= $this->partialView("admincurrentlab.html");
		return $this->contentView($content);
	}
/* Classes */	
	public function editCourses(){
		$content = "";
		// Navbar
		$content .= $this->partialView("coursenav.html");

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
			print $response->ToJSON();
		
			exit;

		}
		else if((isset($_GET['section'])) && ($_GET['section'] == "add")){
			$content .= $this->partialView("courseaddform.html");
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
			$content .= $this->partialView("courselist.html");
		}

		return $this->contentView($content);
	}

/* Users */	
	public function editUsers(){	
		$content = "edit users";
		
		return $this->contentView($content);
	}
/* Reports */	
	public function reportsByClass(){	
		$content = "reportsByClass";
		
		return $this->contentView($content);
	}
	public function reportsByPeriod(){	
		$content = "reportsByPeriod";
		
		return $this->contentView($content);
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
		
		return $this->contentView($content);
	}
	private function partialView($filename){
		$content = file_get_contents("../views/" . $filename);
		$content = str_replace('{base}', $this->config["basePrefix"],$content);
		return $content;
	}
	private function contentView($newcontent){
		// Replace absolute path for a relative path
		$header = $this->partialView("adminheader.html");
		//$header = str_replace('{base}', $this->config["basePrefix"],$header);
		$content = $header;
		// Replace absolute path for a relative path
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
}