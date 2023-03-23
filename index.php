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
    <a href="./newprofile.php" class="nav">New Performer Profile</a>&nbsp;&nbsp;|&nbsp;&nbsp;
    <?php while ($row = $statement->fetch()): ?>
        <ul class = "profile">
            <li><h3><?= $row['stage_name'] ?></h3></li>
            <li><a href="./profile.php?performer_id=<?= $row['performer_id'] ?>">View Profile</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="./edit.php?performer_id=<?= $row['performer_id'] ?>" class="edit">Edit Profile</a></li>
            <li><a href="<?= $row['website'] ?>"><?= $row['website'] ?></a></li>
        </ul>
    <?php endwhile ?>
</body>
</html>
