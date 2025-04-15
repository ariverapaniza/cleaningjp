<?php

// Database configuration - CHANGE THIS WHEN MOVING TO PRODUCTION IN HOSTINGER
$host = 'localhost';
$db   = 'cleaningjp';
$user = 'root';
$pass = '';

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die('Database connection error: ' . $mysqli->connect_error);
}

session_start();

function isLoggedIn()
{
    return isset($_SESSION['UserID']);
}

function isAdmin()
{
    return (isset($_SESSION['IsAdmin']) && $_SESSION['IsAdmin'] == 1);
}
