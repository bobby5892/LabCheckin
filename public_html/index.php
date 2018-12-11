<?php

// ORM - http://propelorm.org
// Routing- 
// update propel command
// Check for changes in schema.xml
// php C:\wamp64\www\LabCheckin\LabCheckin\public_html\vendor\propel\propel\bin\propel.php diff
// Migrate
// php C:\wamp64\www\LabCheckin\LabCheckin\public_html\vendor\propel\propel\bin\propel.php migrate
namespace LabCheck;
require("vendor/autoload.php"); // Load Composer
require("config.php");

session_start();
require("../lib/routes.php");




