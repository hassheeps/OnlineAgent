<?php

/**************** 
    
    Name: Brianne Coleman
    Date: March 21, 2023
    Description: WebDev 2 Final Project - User Registration

****************/

require('connect.php');
session_start();

// Checks whether a user is logged in, and if they have administrative permissions

if(isset($_SESSION['username']) && $_SESSION['user_level_id'] == 2)
{
    //Display all Users

    $query = "SELECT * FROM Users ORDER BY user_id";
    $statement = $db-> prepare($query);
    $statement-> execute();
}

$query = "SELECT * FROM apparatus ORDER BY apparatus_name";

$apparatus_statement = $db->prepare($query);
$apparatus_statement->execute();

if($_POST && isset($_POST['add']) && strlen($_POST['add'] > 0))
{
    $apparatus_name = filter_input(INPUT_POST, 'add', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $query = "INSERT INTO apparatus (apparatus_name) VALUES (:apparatus_name)";

    $new_apparatus = $db->prepare($query);
    $new_apparatus->bindValue(':apparatus_name', $apparatus_name);
    $new_apparatus->execute();

    header('Location: admin.php');
    exit;         
}

if(isset($_POST['delete_apparatus']))
{
    if(isset($_POST['apparatus_checkbox']))
    {
        foreach($_POST['apparatus_checkbox'] as $apparatus_name)
        {
            $query = "DELETE FROM apparatus WHERE apparatus_name = :apparatus_name";

            $deleteapparatus = $db->prepare($query);
            $deleteapparatus->bindValue(':apparatus_name', $apparatus_name);
            $deleteapparatus->execute();
        }
    }

    header("Location: admin.php");
    exit;
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
    <h1>Administrative Tasks</h1><br><br>
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
    <br><br>
    <div class = "apparatus_table">
        <table class = "apparatus">
            <thead> 
            <form method="post">
                <tr>    
                    <th>Act Categories<th>
                </tr>
            </thead>
            <tbody> 
                <?php while($apparatus = $apparatus_statement->fetch()): ?>
                    <tr> 
                        <td width = "615"><?= $apparatus['apparatus_name'] ?></td>
                        <td><input type="checkbox" value="<?= $apparatus['apparatus_name'] ?>" name="apparatus_checkbox[]">
                    </tr>
                <?php endwhile ?>
            </tbody>          
         
        </table><br>


        <input type="submit" id="delete_apparatus" value="Delete Selected Category" name = "delete_apparatus"><br><br>
        </form>
        <form method="post">
        <input id="add" name="add" size="20"></input>&nbsp;&nbsp;<input type="submit" id="add" value="Add New"></input><br><br>
        </form>
            
</body>
</html>
                    
                
            
            
                      <!--  -->