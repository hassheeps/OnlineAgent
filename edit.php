<?php

/*******w******** 
    
    Name: Brianne Coleman
    Date: March 21, 2023
    Description: WebDev 2 - Assignment 3 (Blog)

****************/

require('connect.php');
require('authenticate.php');

// Variables

$performer_id = "";
$error_flag = false;
$error_message = "Title and/or post content cannot be empty.";

// Checks if the post id has been set, retrieves it from the url

if(isset($_GET['performer_id']) && filter_performer_id())
{
    $performer_id = $_GET['performer_id'];
}
else
{
    header('Location: ./index.php');
    exit;
}

// Checks if the "delete" button was what caused the form to submit.

if(isset($_POST['delete']))
{
    // Deletes the query matching the selected post_id from the database

    $query = "DELETE FROM Performers WHERE performer_id = $performer_id";

    $statement = $db->prepare($query);
    $statement->execute();

    header('Location: ./index.php');
    exit;
}
else
{
    // Assumes that if "delete" was not selected, the "post" button was.  Checks and sanitizes all posted inputs.

    if($_POST && isset($_POST['stage_name']) && isset($_POST['contact_phone']) && isset($_POST['contact_email']) && isset($_POST['website']))
    {
        $stage_name = filter_input(INPUT_POST, 'stage_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $contact_phone = filter_input(INPUT_POST, 'contact_phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $contact_email = filter_input(INPUT_POST, 'contact_email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $website = filter_input(INPUT_POST, 'website', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
        if(strlen($stage_name) > 0 && strlen($website) > 0 && strlen($contact_phone) > 0 && strlen($contact_email) > 0)
        {
            $query = "UPDATE performers SET stage_name = :stage_name, website = :website, contact_phone = :contact_phone, contact_email = :contact_email WHERE performer_id = :performer_id";

            $statement= $db->prepare($query);
            $statement->bindValue(':performer_id', $performer_id);
            $statement->bindValue(':stage_name', $stage_name);
            $statement->bindValue(':website', $website);
            $statement->bindValue(':contact_phone', $contact_phone);
            $statement->bindValue(':contact_email', $contact_email);
            $statement->execute();

            $query = "SELECT * FROM Performers WHERE performer_id = $performer_id";

            $statement = $db->prepare($query);
            $statement->execute();

            $post = $statement->fetch();

            
            header('Location: ./index.php');
            exit;
        }
        else
        {
            $error_flag = true;
        }
    }
    else
    {
        // If there was no post action, no changes have been made to the blog post yet.  Display the post as is.

        $query = "SELECT * FROM Performers WHERE performer_id = $performer_id";

        $statement = $db->prepare($query);
        $statement->execute();

        $profile = $statement->fetch();
    }
}

// The function that verifies the post_id

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
    <title>Edit Performer Profile</title>
</head>
<body>
    <h1>Edit Performer Profile</h1>
    <form method="post">
        <input type="hidden" name="performer_id" value="<?= $profile['performer_id'] ?>">
        <?php if(!$error_flag): ?>
            <label for="stage_name">Stage Name:</label>
            <input id="stage_name" name="stage_name" value="<?= $profile['stage_name'] ?>">
            <br><br>
            <label for="website">Website:</label>
            <input id="website" name="website" value="<?= $profile['website'] ?>">
            <br><br>
            <label for="contact_phone">Phone Number:</label>
            <input id="contact_phone" name="contact_phone" value="<?= $profile['contact_phone'] ?>">
            <br><br>
            <label for="contact_email">Email Address:</label>
            <input id="contact_email" name="contact_email" value="<?= $profile['contact_email'] ?>">
            <br><br>
            <input type="submit" name="submit" value="Update Profile">
            <input type="submit" name="delete" value="Delete Profile">
        <?php else: ?>
            <div class = "error">
                <?= $error_message ?>
                <?= $stage_name ?>
                <?= $website ?>
                <?= $contact_email ?>
                <?= $contact_phone ?>
                <br><br><br>
                <a href="./index.php">Return to Home Page</a>
            </div>
        <?php endif ?>
    </form>
</body>
</html>