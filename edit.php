<?php

/*******w******** 
    
    Name: Brianne Coleman
    Date: March 21, 2023
    Description: WebDev 2 Final Project: Edit/Delete performer profile

****************/

require('connect.php');

session_start();

// Variables

$performer_id = "";
$error_flag = false;
$error_message = "Contact details cannot be empty.";

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
    $query = "DELETE FROM Performers WHERE performer_id = $performer_id";

    $statement = $db->prepare($query);
    $statement->execute();

    header('Location: ./index.php');
    exit;
}
else
{
    // Assumes that if "delete" was not selected, the "post" button was.  Checks and sanitizes all posted inputs.

    if($_POST && isset($_POST['stage_name']) && isset($_POST['contact_phone']) && isset($_POST['contact_email']) && isset($_POST['website']) && isset($_POST['bio']))
    {
        $stage_name = filter_input(INPUT_POST, 'stage_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $contact_phone = filter_input(INPUT_POST, 'contact_phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $contact_email = filter_input(INPUT_POST, 'contact_email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $website = filter_input(INPUT_POST, 'website', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $bio = filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Verifies that none of the required post inputs were empty
    
        if(strlen($stage_name) > 0 && strlen($website) > 0 && strlen($contact_phone) > 0 && strlen($contact_email) > 0)
        {
            $query = "UPDATE performers SET stage_name = :stage_name, website = :website, contact_phone = :contact_phone, contact_email = :contact_email, bio = :bio WHERE performer_id = :performer_id";

            // Binds post values to database rows

            $statement= $db->prepare($query);
            $statement->bindValue(':performer_id', $performer_id);
            $statement->bindValue(':stage_name', $stage_name);
            $statement->bindValue(':website', $website);
            $statement->bindValue(':contact_phone', $contact_phone);
            $statement->bindValue(':contact_email', $contact_email);
            $statement->bindValue(':bio', $bio);
            $statement->execute();

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
    <script type="text/javascript" src="script.js"></script>
    <title>Edit Performer Profile</title>
</head>
<body>
    <h1>Edit Performer Profile</h1>
    <div class = "username">
        <?php if(isset($_SESSION['username'])): ?>
            Logged in as <?= $_SESSION['username'] ?>&nbsp;&nbsp;|&nbsp;&nbsp;
            <a href = "./logout.php">Log Out</a>
        <?php endif ?>
    </div>
    <div class="nav">
        <a href="./index.php">Home</a>&nbsp;&nbsp;|&nbsp;&nbsp;
        <a href="./profile.php?performer_id=<?= $profile['performer_id'] ?>">Return to Profile</a>&nbsp;&nbsp;|&nbsp;&nbsp;
        <a href="./newact.php?performer_id=<?= $profile['performer_id'] ?>">Add Act Information</a>
    </div>
    <br>
    <form method="post">
        <input type="hidden" name="performer_id" value="<?= $profile['performer_id'] ?>">
        <?php if(!$error_flag): ?>
            <label for="stage_name">Stage Name:</label>
            <input id="stage_name" name="stage_name" size="50" value="<?= $profile['stage_name'] ?>">
            <br><br>
            <label for="website">Website:</label>
            <input id="website" name="website" size="50" value="<?= $profile['website'] ?>">
            <br><br>
            <label for="contact_phone">Phone Number:</label>
            <input id="contact_phone" name="contact_phone" size="50" value="<?= $profile['contact_phone'] ?>">
            <br><br>
            <label for="contact_email">Email Address:</label>
            <input id="contact_email" name="contact_email" size="50" value="<?= $profile['contact_email'] ?>">
            <br><br>
            <label for="bio">Bio:</label>
            <textarea id="bio" name="bio" rows="10" cols="50"><?= $profile['bio'] ?></textarea>
            <br><br>
            <input type="submit" name="update" id="update" value="Update Profile" onclick="return confirmUpdate()">
            <input type="submit" name="delete" id="delete" value="Delete Profile" onclick="return confirmDelete()">
        <?php else: ?>
            <div class = "error">
                <?= $error_message ?>
            </div>
        <?php endif ?>
    </form>
</body>
</html>