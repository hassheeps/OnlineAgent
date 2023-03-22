<?php

/*******w******** 
    
    Name: Brianne Coleman
    Date: January 30, 2023
    Description: WebDev 2 - Assignment 3 (Blog)

****************/

require('connect.php');
require('authenticate.php');

$stage_name = "";
$website = "";
$contact_phone = "";
$contact_email = "";

// Verifies that a post has occurred and a value exists.  The value is then sanitized to be used as a variable.

if($_POST && !empty($_POST['stage_name']))
{
    $stage_name = filter_input(INPUT_POST, 'stage_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}

if($_POST && !empty($_POST['website']))
{
    $website = filter_input(INPUT_POST, 'website', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}

if($_POST && !empty($_POST['contact_phone']))
{
    $contact_phone = filter_input(INPUT_POST, 'contact_phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}

if($_POST && !empty($_POST['contact_email']))
{
    $contact_email = filter_input(INPUT_POST, 'contact_email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}

// Verifies that the length of both the content and the title variables is 1 or greater.

if(strlen($stage_name) > 0 && strlen($website) > 0 && strlen($contact_phone) > 0 && strlen($contact_email) > 0)
{

    $query = "INSERT INTO performers (stage_name, website, contact_phone, contact_email) VALUES (:stage_name, :website, :contact_phone, :contact_email)";

    $statement= $db->prepare($query);
    $statement->bindValue(':stage_name', $stage_name);
    $statement->bindValue(':website', $website);
    $statement->bindValue(':contact_phone', $contact_phone);
    $statement->bindValue(':contact_email', $contact_email);
    $statement->execute();


    // Adds the new post as a record in the database


    header('Location: ./index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Create Post</title>
</head>
<body>
    <h1>New Performer Profile</h1>
    <a href="./index.php" class="nav">Home Page</a>
    <br><br><br>
    <form method="post" action="newprofile.php">
        <label for="stage_name">Stage Name:</label>
        <input id="stage_name" name="stage_name">
        <label for="website">Website:</label>
        <input id="website" name="website">
        <label for="contact_phone">Phone Number:</label>
        <input id="contact_phone" name="contact_phone">
        <label for="contact_email">Email Address:</label>
        <input id="contact_email" name="contact_email">
        <br><br>
        <input type="submit" value="Submit">

</body>
</html>