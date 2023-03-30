<?php

/**************** 
    
    Name: Brianne Coleman
    Date: March 21, 2023
    Description: WebDev 2 Final Project - Index Page

****************/

require('connect.php');

session_start();

$profile_exists = false;

$query = "SELECT * FROM Performers ORDER BY stage_name DESC";

$statement = $db-> prepare($query);
$statement-> execute();


if(isset($_SESSION['username']))
{
    $username = $_SESSION['username'];

    // retrieves the record from the users table on the database matching the logged in user.

    $query = "SELECT * FROM Users WHERE username = :username";

    $userstatement = $db->prepare($query);
    $userstatement->bindValue(':username', $username);
    $userstatement-> execute();

    $user = $userstatement->fetch();

    $user_id = $user['user_id'];
    $user_level_id = $user['user_level_id'];

    // Checks for records of a performer profile matching the logged in user.

    $query = "SELECT * FROM Performers WHERE user_id = :user_id";

    $profilestatement = $db->prepare($query);
    $profilestatement->bindValue(':user_id', $user_id);
    $profilestatement->execute();

    $profile = $profilestatement->fetch();

    // If no performer profile exists, make the "create performer profile" link visible.

    if($profile != null)
    {
        $profile_exists = true;
    }
}
else
{
    $user = "";
}

?>

<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="main.css">
    <title>Winnipeg Performing Arts collective</title>
</head>
<body>
    <h1>Winnipeg Performers</h1>
    <div class = "username">
        <?php if(isset($_SESSION['username'])): ?>
            Logged in as <?= $_SESSION['username'] ?>&nbsp;&nbsp;|&nbsp;&nbsp;
            <a href = "./logout.php">Log Out</a>
        <?php endif ?>
    </div>
    <div class = "nav">
        <?php if(!isset($_SESSION['username'])): ?>
            <a href="./login.php" class="nav">Log In</a>&nbsp;&nbsp;|&nbsp;&nbsp;
            <a href="./user_registration.php" class="nav">Register</a>&nbsp;&nbsp;|&nbsp;&nbsp;
        <?php endif ?>
        <?php if(!$profile_exists && isset($_SESSION['username'])): ?>
            <a href="./newprofile.php" class="nav">Create Performer Profile</a>&nbsp;&nbsp;|&nbsp;&nbsp;
        <?php endif ?>
        <?php if(isset($_SESSION['username']) && $user_level_id == 2): ?>
            <a href = "./admin.php" class="nav">Manage Users</a>
        <?php endif ?>
    </div>
    <?php while ($row = $statement->fetch()): ?>
        <ul class = "profile">
            <li><h3><?= $row['stage_name'] ?></h3></li>
            <li><a href="./profile.php?performer_id=<?= $row['performer_id'] ?>">View Profile</a>
            <?php if(isset($_SESSION['username']) && $user['user_id'] == $row['user_id']): ?>
                &nbsp;&nbsp;|&nbsp;&nbsp;<a href="./edit.php?performer_id=<?= $row['performer_id'] ?>" class="edit">Edit Profile</a></li>
            <?php endif ?>
            <li><a href="<?= $row['website'] ?>"><?= $row['website'] ?></a></li>
        </ul>
    <?php endwhile ?>
</body>
</html>