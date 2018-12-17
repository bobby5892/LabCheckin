<?php

// ORM - http://propelorm.org
// Routing- 
// update propel command
// Check for changes in schema.xml
// php C:\wamp64\www\LabCheckin\LabCheckin\public_html\vendor\propel\propel\bin\propel.php diff
// Migrate
// php C:\wamp64\www\LabCheckin\LabCheckin\public_html\vendor\propel\propel\bin\propel.php migrate
//propel model:build
namespace LabCheck;

require("vendor/autoload.php"); // Load Composer
// Need to wrangle this down to 1 config.
require("../config.php");
require("generated-conf/config.php");
require("propel.php");

session_start();
require("../lib/routes.php");




