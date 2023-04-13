<?php

/******************
    
	Name: Brianne Coleman
	Date: March 30, 2023 
	Description: Final Project - Edit User Page (Admin)

*******************/

require('connect.php');
session_start();

if(isset($_GET['user_id']) && filter_performer_id())
{
    $user_id = $_GET['user_id'];
}
else
{
    header('Location: ./index.php');
    exit;
}

if(isset($_POST['delete']))
{
    $query = "DELETE FROM Users WHERE user_id = $user_id";

    $statement = $db->prepare($query);
    $statement->execute();

    header('Location: ./admin.php');
    exit;
}
else
{
    // Assumes that if "delete" was not selected, the "post" button was.  Checks and sanitizes all posted inputs.

    if($_POST && isset($_POST['user_id']) && isset($_POST['username']) && isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['user_level_id']))
    {
        $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $user_level_id = filter_input(INPUT_POST, 'user_level_id', FILTER_VALIDATE_INT);

        // Verifies that none of the required post inputs were empty
    
        if(strlen($user_id) > 0 && strlen($username) > 0 && strlen($first_name) > 0 && strlen($last_name) > 0)
        {
            $query = "UPDATE users SET user_id = :user_id, username = :username, first_name = :first_name, last_name = :last_name, email = :email, user_level_id = :user_level_id WHERE user_id = :user_id";

            // Binds post values to database rows

            $statement= $db->prepare($query);
            $statement->bindValue(':user_id', $user_id);
            $statement->bindValue(':username', $username);
            $statement->bindValue(':first_name', $first_name);
            $statement->bindValue(':last_name', $last_name);
            $statement->bindValue(':email', $email);
            $statement->bindValue(':user_level_id', $user_level_id);
            $statement->execute();

            header('Location: ./admin.php');
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

		$query = "SELECT * FROM Users WHERE user_id = $user_id";

		$statement = $db->prepare($query);
		$statement-> execute();

		$user = $statement->fetch();
    }
}

function filter_performer_id()
{
    return filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);
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
    <title>Edit User</title>
</head>
<body>
    <section id = "header">
        <h1>Edit Users</h1>
        <br><br>
        <div class = "navcontainer">
            <div class = "navbox1">
                <a href="./index.php">Home</a>&nbsp;&nbsp;|&nbsp;&nbsp;
            </div>
            <div class = "navbox2">
                <?php if(isset($_SESSION['username'])): ?>
                    Logged in as <?= $_SESSION['username'] ?>&nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href = "./logout.php">Log Out</a>
                <?php endif ?> 
            </div>
        </div>
    </section>
	<form method="post">
		<label for="user_id">User ID:</label>
	    <input id="user_id" name="user_id" size="50" value="<?= $user['user_id'] ?>">
	    <br><br>
		<label for="username">Username Name:</label>
	    <input id="username" name="username" size="50" value="<?= $user['username'] ?>">
	    <br><br>
	    <label for="first_name">First Name:</label>
	    <input id="first_name" name="first_name" size="50" value="<?= $user['first_name'] ?>">
	    <br><br>
	    <label for="last_name">Last Name:</label>
	    <input id="last_name" name="last_name" size="50" value="<?= $user['last_name'] ?>">
	    <br><br>
	    <label for="email">Email Address:</label>
	    <input id="email" name="email" size="50" value="<?= $user['email'] ?>">
	    <br><br>
	    <label for="user_level_id">User level</label>
	   	<select id="user_level_id" name="user_level_id">
	   		<option value="1">User</option>
	        <option value="2">Administrator</option>
	    </select>
	    <br><br>
	    <input type="submit" name="update" id="update" value="Update User" onclick="return confirmUpdate()">
	    <input type="submit" name="delete" id="delete" value="Delete User" onclick="return confirmDelete()">
    </form>
</body>
</html>