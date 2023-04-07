<?php
session_start();
// Constants

define('SERVER_DIRECTORY', dirname ( __FILE__ ).'/server');
define('PUBLIC_DIRECTORY', dirname ( __FILE__ ).'/client');
require_once SERVER_DIRECTORY.'/settings/Environment.class.php';

// Load Environment variables

(new Environment(__DIR__ .'/.env'))->load();

if (getenv('APP_ENV') == "dev") {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

require_once SERVER_DIRECTORY.'/settings/ConnectDB.class.php';
require_once SERVER_DIRECTORY.'/settings/Navigation.class.php';

$dbCon = (new ConnectDB());

if (!$dbCon->testConnection())	{
    print "Error: Could not connect to database. Please try again later.";
    die("Sorry, we are currently experiencing technical difficulties. Please try again later.");
}
$router = (new Navigation());

// Load Main Page
require_once PUBLIC_DIRECTORY.'/index.php';
?>