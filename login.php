<?php

/**************** 
    
    Name: Brianne Coleman
    Date: March 24, 2023
    Description: WebDev 2 Final Project - Log In Page

****************/

require('connect.php');

session_start();

$username = "";
$password = "";
$errorflag = false;

if($_POST && !empty($_POST['username']) && !empty($_POST['password']))
{
	$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$query = "SELECT * FROM users WHERE username = :username";

	$statement = $db->prepare($query);
    $statement->bindValue(':username', $username);
    $statement->execute();

	$user = $statement->fetch();

    if($user == null)
    {
        $errorflag = true;
    }
    else
    {
        if($password == $user['password'])
        {
            $_SESSION['username'] = $username;

            header('Location: ./index.php');
            exit;
        }  
        else
        {
            $errorflag = true;
        }
    }




}

?>

<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Log In</title>
</head>
<body>
	<h1>Log In</h1>
    <div class = "username">
        <?php if($_POST && isset($_SESSION['username'])): ?>
            Logged in as <?= $_SESSION['username'] ?>&nbsp;&nbsp;|&nbsp;&nbsp;
            <a href = "./logout.php">Log Out</a>
        <?php endif ?>
    </div>
    <br>
    <div class = "nav">
        <a href="./index.php">Home</a>
    </div>
    <br>
	<form method="post" action="login.php">
        <label for="username">Username:</label>
        <input id="username" name="username">
        <label for="password">Password:</label>
        <input id="password" name="password">
        <?php if($_POST && $errorflag): ?>
            <p class = "invalidlogin">Invalid log-in credentials.  Please try again.</p>
        <?php endif ?>
        <br><br>
        <input type="submit" value="Submit">
        </body>
</html>
