<?php

/**************** 
    
    Name: Brianne Coleman
    Date: March 21, 2023
    Description: WebDev 2 Final Project - User Registration

****************/

require('connect.php');

session_start();

// error variables

$error_flag = false;
$required_error_flag = false;
$empty = false;
$password_error = "Passwords do not match.  Please try again.";
$required_field_error = "All fields are required.";

// input variables

$username = "";
$password = "";
$confirm_password = "";
$first_name = "";
$last_name = "";
$email = "";



if($_POST && !empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['confirm_password']) && !empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['email']))
{
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $confirm_password = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if($password != $confirm_password)
    {
        $error_flag = true;
    }
}

if(empty($_POST['username']) || empty($_POST['password']) || empty($_POST['confirm_password']) || empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['email']))
{
    $empty = true;
}

if($_POST && $empty)
{
    $required_error_flag = true;
}


if(!$error_flag && $_POST && strlen($username) > 0 && strlen($password) > 0 && strlen($first_name) > 0 && strlen($last_name) > 0 && strlen($email) > 0)
{
    $query = "INSERT INTO users (username, password, first_name, last_name, email) VALUES (:username, :password, :first_name, :last_name, :email)";

    $statement= $db->prepare($query);
    $statement->bindValue(':username', $username);
    $statement->bindValue(':password', $password);
    $statement->bindValue(':first_name', $first_name);
    $statement->bindValue(':last_name', $last_name);
    $statement->bindValue(':email',  $email);
    $statement->execute();

    if(!isset($_SESSION['username']))
    {
        $_SESSION['username'] = $username;
    }

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
        <label for="password">Password:</label>
        <input id="password" name="password"><br>  
        <label for="confirm_password">Confirm Password:</label>
        <input id="confirm_password" name="confirm_password"><br>
        <label for="first_name">First Name:</label>
        <input id="first_name" name="first_name"><br>
        <label for="last_name">Last Name:</label>
        <input id="last_name" name="last_name"><br> 
        <label for="email">Email Address:</label>
        <input id="email" name="email"><br>
        <br>
        <input type="submit" value="Submit">
        <input type="submit" value="Cancel">
    </form>
    <br>
    <div class ='error'>
        <?php if($error_flag): ?>
            <?= $password_error ?>
        <?php endif ?>
        <?php if($required_error_flag): ?>
            <?= $required_field_error ?>
        <?php endif ?>
    </div>
</body>
</html>