<?php

require __DIR__ . "/../controllers/loginController.php";
$loginController = new LoginController($config);

// Add Default User if Admins is blank
$loginController->AddDefaultIfEmpty();

if (isset($_SERVER['REDIRECT_URL'])) {
      $request = $config['basePrefix'] . $_SERVER['REDIRECT_URL'];
   } else {
      $request = $request = $config['basePrefix'] . $_SERVER['REQUEST_URI'];
   }

$content = "";   
switch ($request) {
    case $config['basePrefix'] . '/' :
   		require __DIR__ . "/../controllers/statsController.php";
   		$controller = new StatsController($config);
   		$content =  $controller->publicIndex();
        break;
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
    case $config['basePrefix'] . '/admin/Report/Period' :
        if($loginController->IsLoggedIn()){
          require __DIR__ . "/../controllers/adminController.php";
          $controller = new AdminController($config);
          $content =  $controller->reportsByPeriod();
        }
        else{
         $content =  $loginController->LoginForm();
        }
        break;
     case $config['basePrefix'] . '/admin/Report/Class' :
        if($loginController->IsLoggedIn()){
          require __DIR__ . "/../controllers/adminController.php";
          $controller = new AdminController($config);
          $content =  $controller->reportsByClass();
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
    case $config['basePrefix'] . '/logout' :
         $content =  $loginController->LogOut();

        break;        
    case $config['basePrefix'] .  '' :
        require __DIR__ . '/views/index.php';
        break;
    case $config['basePrefix'] . '/about' :
        require __DIR__ . '/views/about.php';
        break;
    default: 
        require __DIR__ . '/../views/404.php';
        break;
}
print $content;