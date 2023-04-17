<?php

/**************** 
    
    Name: Brianne Coleman
    Date: March 21, 2023
    Description: WebDev 2 Final Project - Index Page

****************/

require('connect.php');
session_start();

$profile_exists = false;

$query = "SELECT * FROM Performers ORDER BY stage_name";

$statement = $db-> prepare($query);
$statement-> execute();


if(isset($_SESSION['username']))
{
    $username = $_SESSION['username'];
    $user_level_id = $_SESSION['user_level_id'];
    $user_id = $_SESSION['user_id'];

    // retrieves the record from the users table on the database matching the logged in user.

    $query = "SELECT * FROM Users WHERE username = :username";

    $userstatement = $db->prepare($query);
    $userstatement->bindValue(':username', $username);
    $userstatement-> execute();

    $user = $userstatement->fetch();

    // Checks for records of a performer profile matching the logged in user.

    $query = "SELECT * FROM Performers WHERE user_id = :user_id";

    $profilestatement = $db->prepare($query);
    $profilestatement->bindValue(':user_id', $user_id);
    $profilestatement->execute();

    $profile = $profilestatement->fetch();
    
    // If a performer profile exists, hide the "create performer profile" link.  Set the performer_id to the existing profile.

    if($profile != null)
    {
        $profile_exists = true;
        $performer_id = $profile['performer_id'];
    }
}
else
{
    $user = "";
}

if($_POST && isset($_POST['search']))
{
    $stage_name = filter_input(INPUT_POST, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    $query = "SELECT * FROM performers WHERE stage_name = :stage_name";

    $searchstatement = $db->prepare($query);
    $searchstatement->bindValue(':stage_name', $stage_name);
    $searchstatement->execute();

    $performer_search = $searchstatement->fetch();

    $apparatus_name = $stage_name;

    $query = "SELECT * FROM apparatus JOIN acts ON apparatus.apparatus_id = acts.apparatus_id JOIN performers ON acts.performer_id = performers.performer_id WHERE apparatus_name = :apparatus_name";

    $actsearchstatement = $db->prepare($query);
    $actsearchstatement->bindValue(':apparatus_name', $apparatus_name);
    $actsearchstatement->execute();
}

?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="main.css">
    <title>Winnipeg Performing Arts collective</title>
</head>
<body>
    <section id = "header">
        <h1>Winnipeg Performers</h1>
        <br><br>
            <div class = "navcontainer">
                <div class = "navbox1">
                    <form method="post">
                        <?php if(!$profile_exists && isset($_SESSION['username'])): ?>
                            <a href="./newprofile.php" class="nav">Create Performer Profile</a>&nbsp;&nbsp|
                        <?php endif ?>
                        <?php if(isset($_SESSION['username']) && $profile_exists): ?>
                            <a href="./profile.php?performer_id=<?= $performer_id ?>">View My Profile</a>&nbsp;&nbsp|
                        <?php endif ?>
                        <?php if(isset($_SESSION['user_level_id']) && $_SESSION['user_level_id'] == 2 ): ?>
                            <a href = "./admin.php" class="nav">Administrative Tasks</a>&nbsp;&nbsp;|
                        <?php endif ?>
                        <input id="search" name="search" size="20"></input>
                        <input type="submit" id="searchbutton" value="Search"></input>
                    </form>
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
    <?php if(!isset($_POST['search'])): ?>
        <?php while ($row = $statement->fetch()): ?>
            <ul class = "profile">
                <li><h3><?= $row['stage_name'] ?></h3></li>
                <li><a href="./profile.php?performer_id=<?= $row['performer_id'] ?>">View Profile</a>
                <?php if(isset($_SESSION['user_level_id']) && $_SESSION['user_level_id'] == 2): ?>
                    &nbsp;|&nbsp;&nbsp;<a href="./edit.php?performer_id=<?= $row['performer_id'] ?>" class="edit">Edit Profile</a></li>
                <?php endif ?>
                <li><a href="#"><?= $row['website'] ?></a></li>
            </ul>
        <?php endwhile ?>
    <?php else: ?>
        <?php if(isset($performer_search['stage_name'])): ?>
            <ul class = "profile">
                <li><h3><?= $performer_search['stage_name'] ?></h3></li>
                <li><a href="./profile.php?performer_id=<?= $performer_search['performer_id'] ?>">View Profile</a></li>
                <?php if(isset($_SESSION['user_level_id']) && $_SESSION['user_level_id'] == 2): ?>
                    <li><a href="./edit.php?performer_id=<?= $performer_search['performer_id'] ?>" class="edit">Edit Profile</a></li>
                <?php endif ?>
                <li><?= $performer_search['bio'] ?></li>
            </ul>
        <?php endif ?>
            <?php while($act = $actsearchstatement->fetch()): ?>
                <ul class = "profile">
                    <li><h3><?= $act['act_name'] ?></h3></li>
                    <li>Performer: <?= $act['stage_name'] ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="./profile.php?performer_id=<?= $act['performer_id'] ?>">View Profile</a></li>
                    <li></li>
                    <li><?= $act['description'] ?></li>
                </ul>
            <?php endwhile ?>
        </ul>
    <?php endif ?>
    <footer>
        <br>
        <p>Winnipeg Performing Arts Collective</p>
        <p>123 Main Street | Winnipeg, Manitoba</p>
        <p>&copy; Copyright 2023</p>
    </footer>
    <p>&nbsp;</p>
</body>
</html>