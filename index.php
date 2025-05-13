<?php
// Include configuration
require_once 'config/config.php';

// Define the current directory as the base directory
define('BASE_DIR', __DIR__);

// Include the home page (not redirect)
include_once 'views/home/home.php';
?>
