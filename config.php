<?php
// config.php

// Database configuration
$host = 'localhost';
$db   = 'cleaningjp';
$user = 'root';
$pass = '';

// Create a new MySQLi connection
$mysqli = new mysqli($host, $user, $pass, $db);

// Check connection
if ($mysqli->connect_error) {
    die('Database connection error: ' . $mysqli->connect_error);
}

// Start session
session_start();

// Function to check if user is logged in
function isLoggedIn()
{
    return isset($_SESSION['UserID']);
}

// Function to check if user is admin
function isAdmin()
{
    return (isset($_SESSION['IsAdmin']) && $_SESSION['IsAdmin'] == 1);
}