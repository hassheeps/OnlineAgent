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

$resized_images = [];

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

$query = "SELECT * FROM acts WHERE performer_id = $performer_id";

$act_statement = $db->prepare($query);
$act_statement->execute();

// Comment form

$captcha_error_flag = false;

if(isset($_SESSION['captcha']))
{
    $captcha_code = $_SESSION['captcha'];
}


if($captcha_error_flag)
{
    $test = 1;
}
else 
{
	$test = 0;
}

$captcha_error = "CAPTCHA Error, please try again.";
$captcha_input = "";
$title = "";
$body = "";
$author = "";

if($_POST && isset($_POST['captcha']))
{
    $captcha_input = filter_input(INPUT_POST, 'captcha', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}

if($_POST && isset($_POST['captcha']) && !isset($_SESSION['username']))
{
    if($captcha_input != $captcha_code)
    {
        $captcha_error_flag = true;
    }
}

if($_POST && isset($_POST['title']) && isset($_POST['body']))
{
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $body = filter_input(INPUT_POST, 'body', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $performer_id = $_GET['performer_id'];
    $user_id = "";
    $author = $_POST['author'];

    if(isset($_SESSION['user_id']) && isset($_SESSION['username']))
    {
        $user_id = $_SESSION['user_id'];
        $author = $_SESSION['username'];
    }

    if($captcha_error_flag == false)
    {
        $query = "INSERT INTO comments (title, body, performer_id, user_id, author) VALUES (:title, :body, :performer_id, :user_id, :author)";

        $commentstatement = $db->prepare($query);
        $commentstatement->bindValue(':title', $title);
        $commentstatement->bindValue(':body', $body);
        $commentstatement->bindValue(':performer_id', $performer_id);
        $commentstatement->bindValue(':user_id', $user_id);
        $commentstatement->bindValue(':author', $author);
        $commentstatement->execute();

        header("Location: ./profile.php?performer_id={$performer_id}");
        exit;
    }
}

// Display comments


$query = "SELECT * FROM comments WHERE performer_id = $performer_id ORDER BY comment_date DESC";

$commentselect = $db->prepare($query);
$commentselect->execute();


// The function that validates the post id

function filter_post_id()
{
    return filter_input(INPUT_GET, 'performer_id', FILTER_VALIDATE_INT);
}

// CAPTCHA

$usable_characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

function generate_string($input, $strength = 10)
{
    $input_length = strlen($input);
    $random_string = '';
    
    for($i = 0; $i < $strength; $i++)
    {
        $random_character = $input[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }

    return $random_string;
}

$string_length = 6;
$captcha_string = generate_string($usable_characters, $string_length); 

$_SESSION['captcha'] = $captcha_string;


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
    <section id = "header">
        <h1><?= $profile['stage_name'] ?></h1>
        <div class = "timestamp">
            Created: <?= $profile['date_created'] ?>
        </div>
        <div class = "navcontainer">
            <div class = "navbox1">
                <a href="./index.php">Home</a>&nbsp;&nbsp;
                <?php if(isset($_SESSION['username']) && $user['user_id'] == $profile['user_id']): ?>
                    |&nbsp;&nbsp;<a href="./edit.php?performer_id=<?= $profile['performer_id'] ?>">Edit</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="./newact.php?performer_id=<?= $profile['performer_id'] ?>">Add Act Information</a>
                <?php endif ?>
                <?php if(isset($_SESSION['user_level_id']) && $_SESSION['user_level_id'] == 2): ?>
                    |&nbsp;&nbsp;<a href="./edit.php?performer_id=<?= $profile['performer_id'] ?>">Edit Profile</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                <?php endif ?>
            </div>
            <div class = "navbox2">
                <?php if(!isset($_SESSION['username'])): ?>
                    <a href="./login.php" class="nav">Log In</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="./user_registration.php" class="nav">Register</a>
                <?php endif ?>
                <?php if(isset($_SESSION['username'])): ?>
                    Logged in as <?= $_SESSION['username'] ?>&nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href = "./logout.php">Log Out</a>
                <?php endif ?> 
            </div>
        </div>
    </section>
    <section id = "profileinfo">
        <div class = "infobox">
            <div class = "photoform">
            <ul> 
                <li><h3>Contact Details</h3></li>
                <li><?= $profile['contact_phone'] ?></li>
                <li><?= $profile['contact_email'] ?></li>
                <li><?= $profile['website'] ?></li>
            </ul>
            </div>
            <div class = "photoform">
            <ul>
                <li><h3>Bio</h3></li>
                <li><?= $profile['bio'] ?></li>
            </ul>
            <br>
            </div>
            <ul>
                <li><h3>Act Information</h3></li>
                <?php while($row = $act_statement->fetch()): ?>
                    <li>Act Name: <?= $row['act_name'] ?></li>
                    <li>Act Description: <?= $row['description'] ?></li>      
                    <br><br>              
            <?php endwhile ?>
            </ul> 
        </div>
    </section>
    <section id = "photos">
        <div class = "images">
            <h3>Photos</h3>
            <br>
            <?php foreach ($resized_images as $resized_image): ?>
                <img src = "<?= $resized_image ?>">
            <?php endforeach ?>
            <br><br>
        </div>
        <div class = "comment_display">
            <h3>Comments</h3>
            <br>
            <?php while ($comment = $commentselect->fetch()): ?>
                <h4><?= $comment['title'] ?></h4><br>
                <p class = "commenttimestamp"><?= $comment['comment_date'] ?></p>
                <p><?= $comment['body'] ?></p>
                <p><h4>- <?= $comment['author'] ?></h4></p><br>
            <?php endwhile ?> 
        </div>
        <div class = "comment_form"><br>
            <h3>Leave a Comment</h3>
            <br>
            <form method = "post">
                <label for = "title">Title:</label>
                <input id = "title" name = "title" size = "50" value="<?= $title ?>"></input><br><br>
                <label for="body">Comment:</label>
                <textarea id="body" name="body" rows="10" cols="50"><?= $body ?></textarea><br><br>
                <?php if(!isset($_SESSION['username'])): ?>
                    <label for = "author">Your Name:</label>
                    <input id = "author" name = "author" size = "50" value="<?= $author ?>"></input><br><br>
                    <label for="captcha">Please Enter the Captcha Text</label>
                    <div class = "captcha"><?= $_SESSION['captcha'] ?><br>
                        <input type="text" id="captcha" name="captcha"><br>
                    </div>
                    <?php if($captcha_error_flag): ?>
                        <p class = "error"><?= $captcha_error ?></p>
                    <?php endif ?>
                <?php endif ?>
                <input type="submit" id="comment" name="comment" value="Submit Comment"><br>
            </form>
        </div>
    </section>
    <footer>
        <br>
        <p>Winnipeg Performing Arts Collective</p>
        <p>123 Main Street | Winnipeg, Manitoba</p>
        <p>&copy; Copyright 2023</p>
    </footer>
    <br><br>
    <p>&nbsp;</p>
</body>
</html>