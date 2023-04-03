<?php

/**************** 
    
    Name: Brianne Coleman
    Date: March 22, 2023
    Description: WebDev2 Final Project - View individual profile

****************/

require('connect.php');
session_start();

$user_id = $_SESSION['user_id'];

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

if(isset($_SESSION['username']))
{
    $username = $_SESSION['username'];

    // retrieves the record from the users table on the database matching the logged in user.

    $query = "SELECT * FROM users WHERE username = :username";

    $statement = $db->prepare($query);
    $statement->bindValue(':username', $username);
    $statement->execute();

    $user = $statement->fetch();

}
else
{
    $user = "";
}

// Retrieves the record from the database that matches the post id.

$query = "SELECT * FROM Performers WHERE performer_id = $performer_id";

$statement = $db->prepare($query);
$statement->execute();

$profile = $statement->fetch();

// Retrieves the images from the database that match the user_id

$query = "SELECT * FROM images WHERE user_id = $user_id";

$image_statement = $db->prepare($query);
$image_statement->execute();

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
    <div class = "username">
        <?php if(isset($_SESSION['username'])): ?>
            Logged in as <?= $_SESSION['username'] ?>&nbsp;&nbsp;|&nbsp;&nbsp;
            <a href = "./logout.php">Log Out</a>
        <?php endif ?>
    </div>
    <div class="nav">
        <a href="./index.php">Home</a>&nbsp;&nbsp;|&nbsp;&nbsp;
        <?php if(isset($_SESSION['username']) && $user['user_id'] == $profile['user_id']): ?>
            <a href="./edit.php?performer_id=<?= $profile['performer_id'] ?>">Edit</a>
        <?php endif ?>
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
    <?php while ($image = $image_statement->fetch()): ?>
        <ul>
            <li><img src= images/<?= $image['filename'] ?>></li>"
        </ul>
        <?php endwhile ?>
</body>
</html>