<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . "/vendor/autoload.php";
require_once './app/config.php';
require_once './app/helpers.php';
require_once './routes/routes.php';

$query = $_GET['route'] ;
new Router($query);


