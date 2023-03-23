<?php

/*******w******** 
    
    Name: Brianne Coleman
    Date: March 22, 2023
    Description: WebDev2 Final Project - View individual profile

****************/

require('connect.php');

// Checks if the post id has been set, retrieves it from the url

if(isset($_GET['performer_id']) && filter_post_id())
{
    $performer_id = $_GET['performer_id'];
}
else
{
    header('Location: ./index.php');
    exit;
}

// Retrieves the record from the database that matches the post id.

$query = "SELECT * FROM Performers WHERE performer_id = $performer_id";

$statement = $db->prepare($query);
$statement->execute();

$profile = $statement->fetch();

// The function that validates the post id

function filter_post_id()
{
    return filter_input(INPUT_GET, 'performer_id', FILTER_VALIDATE_INT);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Profile - <?= $profile['stage_name'] ?></title>
</head>
<body>
    <h1><?= $profile['stage_name'] ?></h1>
    <div class = "timestamp">
        Created: <?= $profile['date_created'] ?>
    </div>
    <div class="nav">
        <a href="./index.php">Home</a>&nbsp;&nbsp;|&nbsp;&nbsp;
        <a href="./edit.php?performer_id=<?= $profile['performer_id'] ?>">Edit</a>
    </div>
    <div class="contact"> 
    <ul> 
        <li><h3>Contact Details:</h3></li>
        <li><?= $profile['contact_phone'] ?></li>
        <li><?= $profile['contact_email'] ?></li>
        <li><?= $profile['website'] ?></li>
    </ul>
    </div>
    <div class="bio">
        <ul>
            <li><h3>Bio</h3></li>
            <li><?= $profile['bio'] ?></li>
        </ul>
    </div>
</body>
</html>