<?php

/*************** 
    
    Name: Brianne Coleman
    Date: April 7, 2023
    Description: WebDev 2 Final Project - Add act information to a profile

****************/

require('connect.php');
session_start();

$performer_id = $_SESSION['performer_id'];

if(isset($_GET['act_id']))
{ 
    $act_id = filter_input(INPUT_GET, 'act_id', FILTER_VALIDATE_INT);
}

//Get performer id for stage name

$query = "SELECT * FROM performers WHERE performer_id = :performer_id";

$statement = $db->prepare($query);
$statement->bindValue(':performer_id', $performer_id);
$statement->execute();

$performer = $statement->fetch();

//Get act id record

$query = "SELECT * FROM Acts WHERE act_id = :act_id";

$statement = $db->prepare($query);
$statement->bindValue(':act_id', $act_id);
$statement->execute();

$act = $statement->fetch();

// Checks if the "delete" button was what caused the form to submit.

if(isset($_POST['act_delete']))
{
    $query = "DELETE FROM acts WHERE act_id = :act_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':act_id', $act_id);
    $statement->execute();

    header("Location: profile.php?performer_id={$performer_id}");
    exit;
}
else
{
    // If it wasn't delete, it was submitted as an update 

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

            $act_query = "UPDATE acts SET act_name = :act_name, description = :description, performer_id = :performer_id, apparatus_id = :apparatus_id, category_id = :category_id WHERE act_id = :act_id";

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
    <script type="text/javascript" src="script.js"></script>
    <title>Create Act</title>
</head>
<body>
    <section id = "header">
            <h1>Edit Act</h1><br><br>
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
    <section id = "profileinfo">
        <div class = "infobox">
            <h3>Stage Name: <?= $performer['stage_name'] ?></h3>
            <br>
            <form method="post">
                <label for="act_name">Act Name:</label>
                <input id="act_name" name="act_name" value="<?= $act['act_name'] ?>"><br><br>
                <label for="category">Category:</label>
                <select id="category" name="category">
                    <option value="Circus">Circus</option>
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
                <textarea id="description" name="description" rows="10" cols="50"><?= $act['description'] ?></textarea><br><br>
                <input type="submit" value="Submit">&nbsp;&nbsp;&nbsp;<input type="submit" id="act_delete" value="Delete" name="act_delete" onclick="return confirmDeleteAct()">
            </form>
            <br>
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