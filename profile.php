<?php

/**************** 
    
    Name: Brianne Coleman
    Date: March 22, 2023
    Description: WebDev2 Final Project - View individual profile

****************/

require('connect.php');
session_start();

if(isset($_SESSION['user_id']))
{
    $user_id = $_SESSION['user_id'];
}

$resized_images = [];

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

$profile_user_id = $profile['user_id'];

// Retrieves the images from the database that match the user_id, stores them in an array

$query = "SELECT * FROM images WHERE user_id = $profile_user_id";

$image_statement = $db->prepare($query);
$image_statement->execute();

$images = [];

while($row = $image_statement->fetch())
{
    $images[] = $row;
}

// Resizing the images for display

for($i=0; $i < count($images); $i++)
{
    $source_img = "images/" . $images[$i]['filename'];
    $destination_img = "images/resized/" . $images[$i]['filename'];

    $size = getimagesize($source_img);
    $width = $size[0];
    $height = $size[1];

    $resize = "0.25";
    $rwidth = ceil($width * $resize);
    $rheight = ceil($height * $resize);

    if(substr($source_img, -3) == "png")
    {
        $original = imagecreatefrompng($source_img);

        $resized = imagecreatetruecolor($rwidth, $rheight);
        imagecopyresampled($resized, $original, 0, 0, 0, 0, $rwidth, $rheight, $width, $height);

        imagepng($resized, $destination_img);
    }
    else 
    {
        $original = imagecreatefromjpeg($source_img);

        $resized = imagecreatetruecolor($rwidth, $rheight);
        imagecopyresampled($resized, $original, 0, 0, 0, 0, $rwidth, $rheight, $width, $height);

        imagejpeg($resized, $destination_img);
    }
  
    $resized_images[] = $destination_img;
}

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
    <?php foreach ($resized_images as $resized_image): ?>
    <img src = "<?= $resized_image ?>">
    <?php endforeach ?>
    <br><br>
</body>
</html>