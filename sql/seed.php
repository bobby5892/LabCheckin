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

Tests by Robert Moore
Intended to run from console.

12/19/2018
*/
////////////////// SEED INITIAL DATA ////////
//ComposerMods
require("../public_html/vendor/autoload.php");

// App config
require("../config.php");
// Propel db settings
require("../public_html/propel.php");
// propel db handler
require("../public_html/generated-conf/config.php");

// check for existing Data
$courses = CourseQuery::create()->count();
$visits = LabVisitQuery::create()->count();

if(($courses != 0) or ($visits != 0)){
	print "\n Courses and Labvisits already exist in database - aborting";
	exit;
}

print "\n Opening Data";
$data = explode("\n", file_get_contents("lab-form-export-2018-12-13.csv"));
$coursesRaw = [];


// Arrays of Models to create
$LabVisits = [];
$Courses = [];


// First Pass - build unique list of courses
for($i=1; $i<count($data);$i++){
	$row = explode(",",$data[$i]);
	if(count($row) == 4){
		if(isset($row[1])){
			$coursesRaw[$row[1]] = $row[1];
		}
		if((int)$row[2]>(int)$row[3]){
			print "\nError line $i";
			print "\n\t" . $row[2] . ">" . $row[3];
			print "\n" . $data[$i];
			exit;
		}
		//Temp
		$checkin = DateTime::createFromFormat('m/j/Y H:i:s',($row[0]." 00:00:00"));
		date_add($checkin, date_interval_create_from_date_string((int)$row[2] ." hours"));
		$checkout = DateTime::createFromFormat('m/j/Y H:i:s',($row[0]." 00:00:00"));
	//	print "\n " . $row[0];
		print "\nCheckin: " . $checkin->format("Y-m-d H:i:s");
		print "\nCheckout: " . $checkout->format("Y-m-d H:i:s");
	}
}

// unique courses
foreach($coursesRaw as $key => $value){
	print "\n" . $key;
	$temp = new Course();
	$temp->setName($key);

	array_push($Courses,$temp);
}


// Validate Courses
foreach($Courses as $course){
	if(!$course->validate()){
		print var_dump($course);
		print "Invalid Course - aborting";
		exit;
	}
}
print "\nCourses Validated";
// Save Courses
foreach($Courses as $course){
	$course->Save();
}
print "\nCourses Saved";

// Second Pass - build unique list of labVisits
for($i=1; $i<count($data);$i++){
	$row = explode(",",$data[$i]);
	//10/30/2018,Data Structures 1 CS 260,11,14
	// $row[0]  11/28/2018
	// $row[1] coursename
	// $row[2] checkin hour 11
	// $row[3] checkout hour 13


	$labVisit = new LabVisit();
	// Parse in
	$checkin = DateTime::createFromFormat('m/j/Y H:i:s',($row[0]." 00:00:00"));
	date_add($checkin, date_interval_create_from_date_string((int)$row[2] ." hours"));
	$checkout = DateTime::createFromFormat('m/j/Y H:i:s',($row[0]." 00:00:00"));
	date_add($checkout, date_interval_create_from_date_string((int)$row[3] ." hours"));
	// to Propel
	print "\nCheckin: " . $checkin->format("Y-m-d H:i:s");
	//date_add($checkin, date_interval_create_from_date_string((int)$row[2] ." hours"));
	
	$labVisit->setCheckin($checkin->format("Y-m-d H:i:s"));
	print "Verify Checkin: " . $checkin->format("Y-m-d H:i:s");
	$labVisit->setCheckout($checkout->format("Y-m-d H:i:s"));
	
	// Lookup the course
	$currentCourse = CourseQuery::create()
	->filterByName($row[1])
	->limit(1)
	->find();
	// set the course
	$labVisit->setCourseid((int)$currentCourse[0]->getId());
	// set the student
	$labVisit->setstudentid("L00000000");
	if(!$labVisit->validate()){
		print "Invalid LabVisit - aborting";
		foreach($labVisit->getValidationFailures() as $failure){
			echo "\n\tProperty ".$failure->getPropertyPath().": ".$failure->getMessage()."\n";
		}
		exit;
	}
	else{
		$labVisit->save();
		
		if($i % 10){ print ".";}
	}
}