<?php

require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../controllers/AuthController.php';


$database = new Database();
$db = $database->getConnection();


$authController = new AuthController($db);


$authController->logout();


redirect('/index.php');
?>
