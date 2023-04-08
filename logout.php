<?php

/**************** 
    
    Name: Brianne Coleman
    Date: March 29,2023
    Description: WebDev 2 Final Project: Log out page

****************/

session_start();

// Clears all previous session variables

$_SESSION['username'] = null;
$_SESSION['user_level_id'] = null;
$_SESSION['user_id'] = null;
$_SESSION['performer_id'] = null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Log Out</title>
</head>
<body>
	<p class = "logout">You have been successfully logged out.<br><br>
		<a href="./index.php">Home</a>&nbsp;&nbsp;|&nbsp;&nbsp;
		<a href="./login.php">Log In</a>
	</p>
</body>
</html>