<?php

/**************** 
    
    Name: Brianne Coleman
    Date: March 22, 2023
    Description: WebDev 2 Final Project: Create new performer profile

****************/

require('connect.php');
session_start();

// Verifies that a post has occurred and a value exists.  The value is then sanitized to be used as a variable.

if($_POST && !empty($_POST['stage_name']) && !empty($_POST['website']) && !empty($_POST['contact_phone']) && !empty($_POST['contact_email']))
{
    $stage_name = filter_input(INPUT_POST, 'stage_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $website = filter_input(INPUT_POST, 'website', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $contact_phone = filter_input(INPUT_POST, 'contact_phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $contact_email = filter_input(INPUT_POST, 'contact_email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $username = $_SESSION['username'];

    $query = "SELECT * FROM users WHERE username = :username";
    $statement = $db->prepare($query);
    $statement->bindValue(':username', $username);
    $statement->execute();
    $user = $statement->fetch();

    $user_id = $user['user_id'];
}

// Verifies that the length of both the content and the title variables is 1 or greater.

if(strlen($stage_name) > 0 && strlen($website) > 0 && strlen($contact_phone) > 0 && strlen($contact_email) > 0)
{
    // Adds the new post as a record in the database

    $query = "INSERT INTO performers (stage_name, website, contact_phone, contact_email, user_id) VALUES (:stage_name, :website, :contact_phone, :contact_email, :user_id)";

    $statement= $db->prepare($query);
    $statement->bindValue(':stage_name', $stage_name);
    $statement->bindValue(':website', $website);
    $statement->bindValue(':contact_phone', $contact_phone);
    $statement->bindValue(':contact_email', $contact_email);
    $statement->bindValue(':user_id', $user_id);
    $statement->execute();

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
    <h1>New Performer Profile</h1><br><br>
        <div class = "navcontainer">
            <div class = "navbox1">
                <a href="./index.php">Home</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                <a href="./profile.php?performer_id=<?= $profile['performer_id'] ?>">Return to Profile</a>&nbsp;&nbsp;|&nbsp;&nbsp;
            </div>
            <div class = "navbox2">
                <?php if(isset($_SESSION['username'])): ?>
                    Logged in as <?= $_SESSION['username'] ?>&nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href = "./logout.php">Log Out</a>
                <?php endif ?> 
            </div>
        </div>
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
    </form>
</body>
</html>