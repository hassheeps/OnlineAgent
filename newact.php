<?php

/*************** 
    
    Name: Brianne Coleman
    Date: March 22, 2023
    Description: WebDev 2 Final Project - Add act information to a profile

****************/

require('connect.php');
session_start();

if(isset($_GET['performer_id']))
{
   $performer_id = filter_input(INPUT_GET, 'performer_id', FILTER_VALIDATE_INT);
}

if($performer_id != null)
{
    $query = "SELECT * FROM Performers WHERE performer_id = :performer_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':performer_id', $performer_id);
    $statement->execute();

    $performer = $statement->fetch();
}

// Verifies that a post has occurred and a value exists.  The value is then sanitized to be used as a variable.

if($_POST && !empty($_POST['act_name']) && !empty($_POST['description']) && !empty($_POST['category']) && !empty($_POST['apparatus']))
{
    $act_name = filter_input(INPUT_POST, 'act_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $apparatus = filter_input(INPUT_POST, 'apparatus', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Verifies that the length of both the content and the title variables is 1 or greater.

    if(strlen($act_name) > 0 && strlen($description) > 0)
    {
        // Get category id number for chosen category

        $category_query = "SELECT * FROM Categories WHERE category_name = :category";
        $category_statement= $db->prepare($category_query);
        $category_statement->bindValue(':category', $category);
        $category_statement->execute();

        $category_fetch = $category_statement->fetch();
        $category_id = $category_fetch['category_id'];

        // Get apparatus id number for chosen apparatus

        $apparatus_query = "SELECT * FROM Apparatus WHERE apparatus_name = :apparatus";
        $apparatus_statement= $db->prepare($apparatus_query);
        $apparatus_statement->bindValue(':apparatus', $apparatus);
        $apparatus_statement->execute();

        $apparatus_fetch = $apparatus_statement->fetch();
        $apparatus_id = $apparatus_fetch['apparatus_id'];
        
        // Adds the new post as a record in the database

        $act_query = "INSERT INTO acts (act_name, description, performer_id, apparatus_id, category_id) VALUES (:act_name, :description, :performer_id, :apparatus_id, :category_id)";

        $act_statement= $db->prepare($act_query);
        $act_statement->bindValue(':act_name', $act_name);
        $act_statement->bindValue(':description', $description);
        $act_statement->bindValue(':performer_id', $performer_id);
        $act_statement->bindValue(':apparatus_id', $apparatus_id);
        $act_statement->bindValue(':category_id', $category_id);
        
        $act_statement->execute();

        header("Location: profile.php?performer_id={$performer_id}");
        exit;
    }
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
<section id = "header">
    <h1>New Act</h1><br><br>
    <div class = "navcontainer">
        <div class = "navbox1">
            <a href="./index.php" class="nav">Home</a>&nbsp;&nbsp;|&nbsp;&nbsp;
            <a href="./profile.php?performer_id=<?= $performer_id ?>">Return to Profile</a>
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
    <h3>Stage Name: <?= $performer['stage_name'] ?></h3>
    <br>
    <form method="post" action="newact.php?performer_id=<?= $performer_id ?>">
        <label for="act_name">Act Name:</label>
        <input id="act_name" name="act_name"><br><br>
        <label for="category">Category:</label>
        <select id="category" name="category">
            <option value="Circus">Circus</option>
        </select><br><br>
        <label for="apparatus">Apparatus</label>
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
        <input type="submit" value="Submit"><br>
        </form>
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