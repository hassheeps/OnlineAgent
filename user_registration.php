<?php

/**************** 
    
    Name: Brianne Coleman
    Date: March 21, 2023
    Description: WebDev 2 Final Project - User Registration

****************/

require('connect.php');

session_start();


if($_POST && !empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['email']))
{
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}

if($_POST && strlen($username) > 0 && strlen($password) > 0 && strlen($first_name) > 0 && strlen($last_name) > 0 && strlen($email) > 0)
{
    $query = "INSERT INTO users (username, password, first_name, last_name, email) VALUES (:username, :password, :first_name, :last_name, :email)";

    $statement= $db->prepare($query);
    $statement->bindValue(':username', $username);
    $statement->bindValue(':password', $password);
    $statement->bindValue(':first_name', $first_name);
    $statement->bindValue(':last_name', $last_name);
    $statement->bindValue(':email',  $email);
    $statement->execute();

    $_SESSION['username'] = $username;

    // Adds the new post as a record in the database

    header('Location: ./index.php');
    exit;
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="main.css">
    <script type="text/javascript" src="script.js"></script>
    <title>Register as User</title>
</head>
<body>
    <h1>Register as User</h1>
    <a href="./index.php" class="nav">Home</a>&nbsp;&nbsp;|&nbsp;&nbsp;
    <br><br><br>
    <form method="post" action="user_registration.php">
    	<label for="user_name">Username:</label>
    	<input id="user_name" name="username">
        <p class="registrationError error" id="user_name_error">* Invalid username</p>
    	<label for="password">Password:</label>
    	<input id="password" name="password"><br>
        <p class="registrationError error" id="password_error">* Invalid password</p>
        <label for="confirm_password">Confirm Password:</label>
        <input id="confirm_password" name="confirm_password"><br>
        <p class = "registrationError error" id="confirmation_password_error">* Passwords don't match</p>
        <label for="first_name">First Name:</label>
        <input id="first_name" name="first_name"><br>
        <p class="registrationError error" id="first_name_error">* Required Field</p>
        <label for="last_name">Last Name:</label>
        <input id="last_name" name="last_name"><br>
        <p class = "registrationError error" id="last_name_error">* Required Field</p>
        <label for="email">Email Address:</label>
        <input id="email" name="email"><br>
        <p class = "registrationError error" id="email_error">* Please enter a valid email address</p>
        <br><br>
        <input type="submit" value="Submit">
        <input type="submit" value="Cancel">
    </form>
</body>
</html>