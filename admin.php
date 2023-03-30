<?php

/**************** 
    
    Name: Brianne Coleman
    Date: March 21, 2023
    Description: WebDev 2 Final Project - User Registration

****************/

require('connect.php');

session_start();

if(isset($_SESSION['username']) && $_SESSION['username'] == "Admin")
{
    $query = "SELECT * FROM Users ORDER BY username DESC";

    $statement = $db-> prepare($query);
    $statement-> execute();

}

$query = "SELECT * FROM Users";

$statement = $db->prepare($query);
$statement->execute();

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
    <h1>Manage Users</h1>
    <div class = "username">
        <?php if(isset($_SESSION['username'])): ?>
            Logged in as <?= $_SESSION['username'] ?>&nbsp;&nbsp;|&nbsp;&nbsp;
            <a href = "./logout.php">Log Out</a>
        <?php endif ?>
    </div>
    <div class = "nav">
        <a href="./index.php">Home</a>
    </div>
    <br>
    <div class = "table">
        <table class = "users">
            <thead>
                <tr>
                    <th id="userid">User ID #</th>
                    <th id="username">User name</th>
                    <th id="firstname">First Name</th>
                    <th id="lastname">Last Name</th>
                    <th id="userlevel">Permissions Level</th>
                    <th id="edit"></th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $statement->fetch()): ?>
                    <tr>
                        <td id="userid"><?= $row['user_id'] ?></td>
                        <td><?= $row['username'] ?></td>
                        <td><?= $row['first_name'] ?></td>
                        <td><?= $row['last_name'] ?></td>
                        <td>
                            <?php if($row['user_level_id'] == 1): ?>
                                User
                            <?php else: ?>
                                Administrator
                            <?php endif ?>
                        </td>
                        <td><a href="./edituser.php?user_id=<?= $row['user_id'] ?>">Edit</td>
                    </tr>
                <?php endwhile ?>
            </tbody>
        </table>
    </div>
</body>
</html>
