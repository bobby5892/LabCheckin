<?php

require __DIR__ . "/../controllers/loginController.php";
$loginController = new LoginController($config);

// Add Default User if Admins is blank
$loginController->AddDefaultIfEmpty();

if (isset($_SERVER['REDIRECT_URL'])) {
      $request = $config['basePrefix'] . $_SERVER['REDIRECT_URL'];
   } else {
      $request = $config['basePrefix'] . $_SERVER['REQUEST_URI'];
   }

$content = "";   
switch ($request) {
  
    case $config['basePrefix'] .  '/admin' :
        if($loginController->IsLoggedIn()){
          require __DIR__ . "/../controllers/adminController.php";
          $controller = new AdminController($config);
          $content =  $controller->publicIndex();
        }
        else{
         $content =  $loginController->LoginForm();
        }
        break;
    case $config['basePrefix'] . '/admin/' :
     if($loginController->IsLoggedIn()){
          require __DIR__ . "/../controllers/adminController.php";
          $controller = new AdminController($config);
          $content =  $controller->publicIndex();
        }
        else{
         $content =  $loginController->LoginForm();
        }
        break;
    case $config['basePrefix'] . '/admin/login' :   
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {   
          $content =  $loginController->Login();
        }
        else{
          $content =  $loginController->LoginForm();
        }
        break;
    case $config['basePrefix'] . '/admin/Report/Table' :
        if($loginController->IsLoggedIn()){
          require __DIR__ . "/../controllers/statsController.php";
          $controller = new StatsController($config);
          $content =  $controller->reportsByTable();
        }
        else{
         $content =  $loginController->LoginForm();
        }
        break;
      case $config['basePrefix'] . '/admin/getDateRange' :
        if($loginController->IsLoggedIn()){
          require __DIR__ . "/../controllers/statsController.php";
          $controller = new StatsController($config);
          $content =  $controller->getDateRange();
        }
        else{
         $content =  $loginController->LoginForm();
        }
        break;        
     case $config['basePrefix'] . '/admin/getData' :
        if($loginController->IsLoggedIn()){
          require __DIR__ . "/../controllers/statsController.php";
          $controller = new StatsController($config);
          $content =  $controller->getData();
        }
        else{
         $content =  $loginController->LoginForm();
        }
        break;         
     case $config['basePrefix'] . '/admin/Report/Chart' :
        if($loginController->IsLoggedIn()){
          require __DIR__ . "/../controllers/statsController.php";
          $controller = new StatsController($config);
          $content =  $controller->reportsByChart();
        }
        else{
         $content =  $loginController->LoginForm();
        }
        break;   
     case $config['basePrefix'] . '/admin/search' :
        if($loginController->IsLoggedIn()){
          require __DIR__ . "/../controllers/adminController.php";
          $controller = new AdminController($config);
          $content =  $controller->Search();
        }
        else{
         $content =  $loginController->LoginForm();
        }
        break;  
     case $config['basePrefix'] . '/admin/editcourses' :
        if($loginController->IsLoggedIn()){
          require __DIR__ . "/../controllers/adminController.php";
          $controller = new AdminController($config);
          $content =  $controller->editCourses();
        }
        else{
         $content =  $loginController->LoginForm();
        }
        break;
      case $config['basePrefix'] . '/admin/getlivelab' :
        if($loginController->IsLoggedIn()){
          require __DIR__ . "/../controllers/adminController.php";
          $controller = new AdminController($config);
          $content =  $controller->getLiveLab();
        }
        else{
         $content =  $loginController->LoginForm();
        }
        break;     
        case $config['basePrefix'] . '/admin/getcourses' :
        if($loginController->IsLoggedIn()){
          require __DIR__ . "/../controllers/adminController.php";
          $controller = new AdminController($config);
          $content =  $controller->getCourses();
        }
        else{
         $content =  $loginController->LoginForm();
        }
        break;            
      case $config['basePrefix'] . '/admin/editusers' :
        if($loginController->IsLoggedIn()){
          require __DIR__ . "/../controllers/adminController.php";
          $controller = new AdminController($config);
          $content =  $controller->editUsers();
        }
        else{
         $content =  $loginController->LoginForm();
        }
        break;
      case $config['basePrefix'] . '/courses' :
          require __DIR__ . "/../controllers/checkinController.php";
          $controller = new CheckinController($config);
          $content =  $controller->GetCourses();
        break;
     case $config['basePrefix'] . '/savecheckin' :
          require __DIR__ . "/../controllers/checkinController.php";
          $controller = new CheckinController($config);
          $content =  $controller->SaveCheckIn();
        break;
    case $config['basePrefix'] . '/savecheckout' :
          require __DIR__ . "/../controllers/checkinController.php";
          $controller = new CheckinController($config);
          $content =  $controller->SaveCheckOut();
        break;                        
    case $config['basePrefix'] . '/logout' :
         $content =  $loginController->LogOut();

        break;        
    case $config['basePrefix'] .  '' :
        require __DIR__ . "/../controllers/checkinController.php";
          $controller = new CheckinController($config);
          $content =  $controller->Index();
        break;
    case $config['basePrefix'] . '/validateL' :
       require __DIR__ . "/../controllers/checkinController.php";
          $controller = new CheckinController($config);
          $content =  $controller->ValidateL();
    break;
    case $config['basePrefix'] . '/isCheckedIn' :
       require __DIR__ . "/../controllers/checkinController.php";
          $controller = new CheckinController($config);
          $content =  $controller->IsCheckedIn();
    break;                        
    case $config['basePrefix'] . '/' :
       require __DIR__ . "/../controllers/checkinController.php";
          $controller = new CheckinController($config);
          $content =  $controller->Index();
    break;        
    default: 
        require __DIR__ . '/../views/404.php';
        break;
}
print $content;