<?php
error_reporting(E_ERROR | E_PARSE);

require_once("dblib.php");
require_once("misc.php");

// Credentials for MYSQL database
$dbhost = 'localhost';
$dbname = 'asterisk';
$dbuser = 'asteriskuser';
$dbpass = 'asterpass';

// Credentials for AMI (for the realtime tab to work)
// See /etc/asterisk/manager.conf

$manager_host   = "127.0.0.1";
$manager_user   = "pbx-manager-dev";
$manager_secret = "p_ssw0rd";

// Available languages "es", "en", "ru", "de", "fr"
$language = "ru";

require_once("lang/$language.php");

$midb = new dbcon($dbhost, $dbuser, $dbpass, $dbname, true);

$self = $_SERVER['PHP_SELF'];

$DB_DEBUG = false; 

session_start();
//session_register("QSTATS");
header('content-type: text/html; charset: utf-8'); 

?>
