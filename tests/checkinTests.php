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
//ComposerMods
require("../public_html/vendor/autoload.php");

// App config
require("../config.php");
// Propel db settings
require("../public_html/propel.php");
// propel db handler
require("../public_html/generated-conf/config.php");

// package we're testing
require("../controllers/checkinController.php");

////////////////////////////////////////////////
/// Test Checkin Validate					 ///
////////////////////////////////////////////////
try{
	$labVisit = new LabVisit();
	$labVisit->setStudentid("L00000000");
	$labVisit->setCheckin(new DateTime("now"));
	$labVisit->setCourseid(1);

	if($labVisit->validate()){
		rassert(true,"Checkin Validation");
	}
	else{
		rassert(false,"Checkin Validation");
	}
}
catch(Exception $ex){
	rassert(false,"Checkin Validate - $ex");
}
////////////////////////////////////////////////
/// Test Checkin Save    					 ///
////////////////////////////////////////////////
try{
	$labVisit = new LabVisit();
	$labVisit->setStudentid("L00000000");
	$labVisit->setCheckin(new DateTime("now"));
	$labVisit->setCourseid(1);
	$labVisit->save();

	$L00labVisits = LabVisitQuery::create()
	->filterByStudentid('L00000000')
	->find();

	if(count($L00labVisits) == 1){
		rassert(true,"Checkin of student L00000000 saved");
	}
	else{
		rassert(false,"Checkin of student L00000000 did not save");
	}

	// Cleanup back to known state
	foreach($L00labVisits as $visit){
		$visit->delete();
	}
}
catch(Exception $ex){
	rassert(false,"Test Checkin Save  - $ex");
}
////////////////////////////////////////////////
/// Test IsCheckin     					     ///
////////////////////////////////////////////////
try{
	$labVisit = new LabVisit();
	$labVisit->setStudentid("L00000000");
	$labVisit->setCheckin(new DateTime("now"));
	$labVisit->setCourseid(1);
	$labVisit->save();

	$L00labVisits = LabVisitQuery::create()
	->filterByStudentid('L00000000')
	->find();

	$controller = new CheckinController($config);
	
	rassert($controller->checkedIn($L00labVisits[0]),"IsCheckin of L00000000");

	// Cleanup back to known state
	foreach($L00labVisits as $visit){
		$visit->delete();
	}
}
catch(Exception $ex){
	rassert(false,"Checkin Validate - $ex");
}

////////////////////////////////////////////////
/// Test IsCheckin For non logged in student    					     ///
////////////////////////////////////////////////
try{
	$labVisit = new LabVisit();
	$labVisit->setStudentid("L00000001");
	$labVisit->setCheckin(new DateTime("now"));
	$labVisit->setCourseid(1);
	



	$controller = new CheckinController($config);
	
	rassert(!$controller->checkedIn($labVisit),"IsCheckin of L00000001 - non existent student is not logged in");

}
catch(Exception $ex){
	rassert(false,"Checkin Validate - $ex");
}

////////////////////////////////////////////////
/// Test checkout    					     ///
////////////////////////////////////////////////
try{
	$labVisit = new LabVisit();
	$labVisit->setStudentid("L00000001");
	$labVisit->setCheckin(new DateTime("now"));
	$labVisit->setCourseid(1);
	$labVisit->save();

	$controller = new CheckinController($config);
	// will come from post
	$_POST['studentid'] ="L00000001";
	$response = json_decode($controller->SaveCheckOut());
	rassert($response->success,"Checkout - checked out");

	//Cleanup
	$L00labVisits = LabVisitQuery::create()
	->filterByStudentid("L00000001")
	->find();
	foreach($L00labVisits as $visit){
		$visit->delete();
	}
}
catch(Exception $ex){
	rassert(false,"Checkin checkout - $ex");
}


/////////////////////////////////////////////
function rassert($bool,$string){
	$output = "";
	if(!$bool){
		$output .= "[ \033[0;31m FAILED\033[0m ] ";
	}
	else{
		$output .= "\033[0;32m success\033[0m | ";
	}
	$output .= $string . "\n";
	print $output;
}