<?php
require("../models/Response.php");
require('../controllers/templateController.php');
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
use Propel\Runtime\ActiveQuery\Criteria;
class StatsController {
	function __construct($config){
		$this->config = $config;
		$this->template = new TemplateController($config);
	}
	public function getDateRange(){
		// This works off checkin dates
		print json_encode($this->dataDateRange());
		exit;
	}
	public function getData(){
		$dateDomain = $this->dataDateRange();
		// Get all Courses
	
		$response = new Response();
		// Lets Make sure we have a date
		if(!isset($_POST['startDate'])){
			$response->SetSuccess(false);
			$response->SetResponse("You must specify a start Date");
			print $response->ToJSON();
			exit;
		}
		else if(!isset($_POST['endDate'])){
			$response->SetSuccess(false);
			$response->SetResponse("You must specify an end Date");
			print $response->ToJSON();
			exit;	
		}
		// Lets validate the date
		if(
			(!$this->validateDate($_POST['startDate'])) or 
				(!$this->validateDate($_POST['endDate']))
			){
				$response->SetSuccess(false);
				$response->SetResponse("Invalid Date Format");
				print $response->ToJSON();
				exit;
		}
		// Build dates in our format
		$startDate = DateTime::createFromFormat('m-d-Y', $_POST['startDate']);
		$endDate = DateTime::createFromFormat('m-d-Y', $_POST['endDate']);

		$dateDomainStartDate = DateTime::createFromFormat('m-d-Y', $dateDomain['startDate']);
		$dateDomainEndDate = DateTime::createFromFormat('m-d-Y', $dateDomain['endDate']);
	
	
		// Check date for logistical errors
		if($startDate < $dateDomainStartDate){
			// Happened before the first record in database - and is outside the domain range
			$response->SetSuccess(false);
			$response->SetResponse("Date range is prior to first lab visit");
			print $response->ToJSON();
			exit;
		}
		else if($endDate > $dateDomainEndDate){
			// Date has not happened yet
			$response->SetSuccess(false);
			$response->SetResponse("Date range is in future");
			print $response->ToJSON();
			exit;
		}
		else if($startDate > $endDate){
			$response->SetSuccess(false);
			$response->SetResponse("Date range ended before it started - swap your start/end date");
			print $response->ToJSON();
			exit;
		}
		// Ok lets build the aggragated data
		$content = array( 
			"startDate" => $startDate->format("m-d-Y"),
			"endDate" => $endDate->format("m-d-Y"),
			"Count" => 0,
			"Data" => array()
		);

		// Get courses
		$courses = CourseQuery::create()->find();
		
		// Now we are modifying the startDate to account for the current day - so we're going to sub 1 day which is basically midnight because the hours are 0'ed out
		$startDate = date_sub($startDate,date_interval_create_from_date_string('1 day'));
		foreach($courses as $course){
			$countVisits = LabVisitQuery::create()
			->filterByCheckin(array("min" => $startDate->format("Y-m-d H:i:s"), "max" => $endDate->format("Y-m-d H:i:s")))
			->filterByCourseid($course->getId())
			->groupByCourseid($course->getId())
			->count();	

			// Lets not include courses that have 0 visits
			if($countVisits > 0){
				$temp = array(
					"count" => $countVisits,
					"course" => $course->getName()
				);
				// add the data to array
				array_push($content["Data"],$temp);
				// increment the number of items
				$content["Count"]++;
			}
			
		}
		print json_encode($content,JSON_PRETTY_PRINT);
		exit;
	
	}
/* Reports */	
	public function reportsByChart(){	
		// Get the list of dates
		$content = "";
		$chart = $this->template->partialView("adminreportbychart.html");
		$content .= $this->setupPicker($chart);

		return $this->template->adminContentView($content);
	}
	public function reportsByTable(){	
		$content = "";
		$table = $this->template->partialView("adminreportbytable.html");
		$content .= $this->setupPicker($table);

		return $this->template->adminContentView($content);
	}	
	private function setupPicker($content){
		// Lets wrangle the dates from m-d-y to 2018-12-31
		$domainDateRange = $this->dataDateRange();
		$dateDomainStartDate = DateTime::createFromFormat('m-d-Y', $domainDateRange['startDate']);
		$dateDomainEndDate = DateTime::createFromFormat('m-d-Y', $domainDateRange['endDate']);
		$startDatePicker = $dateDomainStartDate->format("Y-m-d");
		$endDatePicker = $dateDomainEndDate->format("Y-m-d");
		// lets put it in
		$content = str_replace('{min}', $startDatePicker,$content);
		$content = str_replace('{max}', $endDatePicker,$content);
		
		// lets figure out dates - current month to date
		$currentMonth = new DateTime("now");
		

		// now for a starting values
		$content = str_replace('{startValue}', $currentMonth->format("Y-m-01"),$content);
		$content = str_replace('{endValue}', $currentMonth->format("Y-m-d"),$content);

		return $content;

	}
	public function publicIndex(){
		return "Welcome";
	}
	private function dataDateRange(){
		$mostRecent = new DateTime("now");
		$mostRecent = date_format($mostRecent, 'm-d-Y');

		$oldest = LabVisitQuery::create()->
		orderByRank('asc')->
		Limit(1)->find();

		//print var_dump($mostRecent);
		$content = array(
			"startDate" => date_format($oldest[0]->getCheckin(), 'm-d-Y'),
			"endDate" => $mostRecent
		);
		return $content;
	}
	//https://stackoverflow.com/questions/19271381/correctly-determine-if-date-string-is-a-valid-date-in-that-format
	private function validateDate($date, $format = 'm-d-Y')
	{
	    $d = DateTime::createFromFormat($format, $date);
	    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
	    return $d && $d->format($format) === $date;
	}	
}