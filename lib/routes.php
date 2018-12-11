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
    case '/' :
   		require __DIR__ . "/../controllers/statsController.php";
   		$controller = new StatsController($config);
   		$content =  $controller->publicIndex();
        break;
    case '/admin' :
        if($loginController->IsLoggedIn()){
          require __DIR__ . "/../controllers/adminController.php";
          $controller = new AdminController($config);
          $content =  $controller->publicIndex();
        }
        else{
         $content =  $loginController->LoginForm();
        }
        break;
    case '/admin/login' :   
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {   
          $content =  $loginController->Login();
        }
        else{
          $content =  $loginController->LoginForm();
        }
        break;
    case '' :
        require __DIR__ . '/views/index.php';
        break;
    case '/about' :
        require __DIR__ . '/views/about.php';
        break;
    default: 
        require __DIR__ . '/views/404.php';
        break;
}
print $content;