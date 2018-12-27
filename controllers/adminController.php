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
			return $response->ToJSON();
		

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
			return json_encode($output);
			
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
		if(isset($_POST['action']) && ($_POST['action'] == "delete")){
			// To Do
			
			if((integer) $_POST['id'] > 1){
				$admin = AdminQuery::create()
				->filterById($_POST['id'])
				->find();

				// Did not find a user
				if($admin->isEmpty()){
					$response = new Response(false, "Did not find a user with that id");
					return $response->ToJSON();
				}
				else{
					$admin[0]->delete();
					$response = new Response(true, "Successfully Deleted");
					return $response->ToJSON();
				}
			}
			else{
				$response = new Response(false,"Cannot Delete Super User");
				return $response->ToJSON();
			}
		}
		else if(isset($_POST['action']) && ($_POST['action'] == "changePass")){
			if((integer) $_POST['id'] > 0){
				$admin = AdminQuery::create()
				->filterById($_POST['id'])
				->find();
				// If there are results
				if(!$admin->isEmpty()){
					$loginController = new LoginController($this->config);
					$admin[0]->setPasswordhash = $loginController->HashPass($_POST['password']);
					if($admin[0]->validate()){
						// only super user can edit super user
						if(($_POST['id'] == 1) && ($_SESSION['USER']['id'] != 1)){
							$response = new Response(false,"Unauthorized");
							return $response->ToJSON();
						}
						$admin[0]->save();
						$response = new Response(true,"Saved");
						return $response->ToJSON();
					}
					else{
						$response = new Response(false,"Bad Password");
						return $response->ToJSON();
					}
				}
				else{
					$response = new Response(false,"Cannot find that user");
					return $response->ToJSON();
				}
			}
			else{
				$response = new Response(false,"Cannot find that user");
				return $response->ToJSON();
			}
		}
		else{
			$content = $this->template->PartialView("admineditusers.html");
			return $this->template->adminContentView($content);	
		}
	}
	public function addUsers(){
		if(isset($_POST['action']) && ($_POST['action'] == "save")){
			$user = new Admin();
			$user->setName($_POST['firstAndLastName']);
			$user->setemailAddress(strtolower($_POST['emailAddress']));
			
			$loginController = new LoginController($this->config);
			$user->setPasswordhash($loginController->HashPass($_POST['password']));

			if($_POST['password'] != $_POST['confirmPassword']){
				$content = "<section>Password and confirm password did not match - press back and try again</section>"; 
				return $this->template->adminContentView($content);
			}
			if($user->validate()){
				// Check for duplicates
				$admin = AdminQuery::create()
				->filterByemailAddress($_POST['emailAddress'])
				->find();
				// See if it already exists
				if($admin->IsEmpty()){
					$content = "<section>User added Successfully</section>";
					$user->save();
					return $this->template->adminContentView($content);
				}
				else{
					$content = "<section>Email already exists</section>"; 
					return $this->template->adminContentView($content);
				}
			}
			else{
				$content = "<section>Invalid characters in new user - press back and try again";
				  foreach ($user->getValidationFailures() as $failure) {
     			   $output .= "Field ".$failure->getPropertyPath().": ".$failure->getMessage()."\n";
    			 }
    			 $content .= "</section";

				return $this->template->adminContentView($content);
			}
		}
		else{
			$content = $this->template->PartialView("adminadduser.html");
			return $this->template->adminContentView($content);		
		}
	}
	public function getUsers(){
		$output =[];
		$output['data'] = [];
		// query courses
		$admins = AdminQuery::create()->find();
		// stack in array
		foreach($admins as $admin) {
		
		  array_push($output['data'], array("ID" => (integer) $admin->getId(),
		  	"Name" => (string) $admin->GetName(),
		  	"EmailAddress" => (string) $admin->GetEmailAddress()
		  ));
		}
		$output["count"] = count($output["data"]);
		return json_encode($output);
		

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
		$courses = json_decode($this->getCourses());
		//print var_dump($courses);
		//$courses["data"]["id"]
		$labVisits = LabVisitQuery::create()
		->filterBystudentid($_POST['searchQuery'])
		->find();
		$content = $this->template->PartialView("adminsearch.html");
		
		if($labVisits->IsEmpty()){
			$content .= "No Lab Visits for " . $_POST['searchQuery'];
		}
		else{
			foreach($labVisits as $labvisit){
				$content .= "<h3>for " . $_POST['searchQuery'] . "</h3>";
				$content .= "<table><tr><th>Time In</th><th>Time Out</th><th>Course</th></tr>";
				$courseName = "";
				foreach($courses->data as $course){
					if($course->id == $labvisit->getCourseid()){
						$courseName = $course->name;
					}
				}
				$content .= "<tr><td>" . $labvisit->getCheckin()->format('Y-m-d H:i:s') . "</td><td>" . $labvisit->getCheckout()->format('Y-m-d H:i:s') ."</td><td>" . $courseName ."</td></tr>";
			}
			$content .= "</table>";
		}		

		$content .= "</section>";
		return $this->template->adminContentView($content);
	}

}