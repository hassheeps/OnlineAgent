<?php

/**************** 
    
    Name: Brianne Coleman
    Date: March 21, 2023
    Description: WebDev 2 Final Project - Index Page

****************/

require('connect.php');

$query = "SELECT * FROM Performers ORDER BY stage_name DESC";

$statement = $db-> prepare($query);
$statement-> execute();


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
    <a href="./newprofile.php" class="nav">New Performer Profile</a>
    <a href="./newact.php" class="nav">Add Act Information</a>
    <?php while ($row = $statement->fetch()): ?>
        <ul class = "profile">
            <li><a href="./edit.php?performer_id=<?= $row['performer_id'] ?>" class="edit">Edit Profile</a></li>      
            <li><?= $row['stage_name'] ?></li>
            <li><?= $row['website'] ?></li>
            <li><?= $row['contact_phone'] ?></li>
            <li><?= $row['contact_email'] ?></li>
        </ul>
    <?php endwhile ?>
</body>
</html>
