<?php

/**************** 
    
    Name: Brianne Coleman
    Date: March 21, 2023
    Description: WebDev 2 Final Project - User Registration

****************/

require('connect.php');

session_start();

// Checks whether a user is logged in, and they have administrative permissions

if(isset($_SESSION['username']) && $_SESSION['user_level_id'] == 2)
{
    //Display all Users

    $query = "SELECT * FROM Users ORDER BY user_id";

    $statement = $db-> prepare($query);
    $statement-> execute();
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
<section id = "header">
    <h1>Manage Users</h1><br><br>
        <div class = "navcontainer">
            <div class = "navbox1">
                <a href="./index.php">Home</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                <a href="./user_registration.php">Create New User</a>
            </div>
            <div class = "navbox2">
                <?php if(isset($_SESSION['username'])): ?>
                    Logged in as <?= $_SESSION['username'] ?>&nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href = "./logout.php">Log Out</a>
                <?php endif ?> 
            </div>
        </div>
    </section>
    <br>
    <div class = "table">
        <table class = "users">
            <thead>
                <tr>
                    <th>User ID #</th>
                    <th>User name</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>User Level</th>
                    <th>&nbsp;</th>
                </tr>
                </thead>
            <tbody>
                <?php while($row = $statement->fetch()): ?>
                    <tr>
                        <td width="100" id="center"><?= $row['user_id'] ?></td>
                        <td width="100" id="center"><?= $row['username'] ?></td>
                        <td width="100" id="center"><?= $row['first_name'] ?></td>
                        <td width="100" id="center"><?= $row['last_name'] ?></td>
                        <td width="100" id="center">
                            <?php if($row['user_level_id'] == 1): ?>
                                User
                            <?php else: ?>
                                Administrator
                            <?php endif ?>
                        </td>
                        <td width="100" id="center"><a href="./edituser.php?user_id=<?= $row['user_id'] ?>">Edit</td>
                    </tr>
                <?php endwhile ?>
            </tbody>
        </table>
    </div>
</body>
</html>
