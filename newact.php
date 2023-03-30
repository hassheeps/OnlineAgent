<?php

/*************** 
    
    Name: Brianne Coleman
    Date: March 22, 2023
    Description: WebDev 2 Final Project - Add act information to a profile

****************/

require('connect.php');

session_start();


if(isset($_GET['performer_id']) && filter_performer_id())
{
    $performer_id = $_GET['performer_id'];
}
else
{
    header('Location: ./index.php');
    exit;
}

$act_name = "";
$description = "";
$category_id = "";
$apparatus_id = "";
$stage_name = "";

$query = "SELECT * FROM Performers WHERE performer_id = $performer_id";

$statement = $db->prepare($query);
$statement->execute();

$performer = $statement->fetch();

// Verifies that a post has occurred and a value exists.  The value is then sanitized to be used as a variable.

if($_POST && !empty($_POST['act_name']) && !empty($_POST['description']) && !empty($_POST['category']) && !empty($_POST['apparatus']))
{
    $act_name = filter_input(INPUT_POST, 'act_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $contact_phone = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $contact_email = filter_input(INPUT_POST, 'apparatus', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}

// Verifies that the length of both the content and the title variables is 1 or greater.

if(strlen($act_name) > 0 && strlen($description) > 0 && strlen($category) > 0 && strlen($apparatus) > 0)
{
    // Get category id number for chosen category

    $query = "SELECT * FROM Categories WHERE category_name = $category";
    $statement= $db->prepare($query);
    $statement->execute();

    $category_fetch = $statement->fetch();
    $category_id = $category_fetch['category_id'];

    // Get apparatus id number for chosen apparatus

    $query = "SELECT * FROM Apparatus WHERE apparatus_name = $apparatus";
    $statement= $db->prepare($query);
    $statement->execute();

    $apparatus_fetch = $statement->fetch();
    $apparatus_id = $apparatus_fetch['apparatus_id'];

    $query = "INSERT INTO acts (act_name, description, category_id, apparatus_id) VALUES (:act_name, :description, :category_id, :apparatus_id";

    $statement= $db->prepare($query);
    $statement->bindValue(':act_name', $act_name);
    $statement->bindValue(':descrption', $description);
    $statement->bindValue(':category_id', $category_id);
    $statement->bindValue(':apparatus_id', $apparatus_id);
    $statement->execute();

    // Adds the new post as a record in the database

    header('Location: ./index.php');
    exit;
}

function filter_performer_id()
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
    <title>Create Act</title>
</head>
<body>
    <h1>New Act</h1>
    <div class = "username">
        <?php if(isset($_SESSION['username'])): ?>
            Logged in as <?= $_SESSION['username'] ?>&nbsp;&nbsp;|&nbsp;&nbsp;
            <a href = "./logout.php">Log Out</a>
        <?php endif ?>
    </div>
    <div class = "nav">
        <a href="./index.php" class="nav">Home</a>&nbsp;&nbsp;|&nbsp;&nbsp;
        <a href="./profile.php?performer_id=<?= $performer_id ?>">Return to Profile</a>
    </div>
    <br><br>
    <h3>Stage Name: <?= $performer['stage_name'] ?></h3>
    <br>
    <form method="post" action="newact.php">
        <label for="act_name">Act Name:</label>
        <input id="act_name" name="act_name"><br><br>
        <label for="category">Category:</label>
        <select id="category" name="category">
            <option value="Circus">Circus</option>
            <option value="Dance">Dance</option>
            <option value="Music">Music</option>
            <option value="Magic">Magic</option>
            <option value="Comedy">Comedy</option>
            <option value="Variety">Variety</option>
        </select><br><br>
        <label for="apparatus" name="apparatus">Apparatus</label>
        <select id="apparatus" name="apparatus">
            <option value="Silks">Silks</option>
            <option value="Handbalance/Hand-to-Hand">Handbalance/Hand-to-Hand</option>
            <option value="Aerial Hoop">Aerial Hoop</option>
            <option value="Lollipop">Lollipop</option>
            <option value="Contortion">Contortion</option>
            <option value="Juggling">Juggling</option>
            <option value="Trapeze">Trapeze</option>
            <option value="Cyr Wheel">Cyr Wheel</option>
        </select><br><br>
        <label for="description">Act Description:</label>
        <textarea id="description" name="description" rows="10" cols="50"></textarea><br><br>
        <input type="submit" value="Submit">

</body>
</html>