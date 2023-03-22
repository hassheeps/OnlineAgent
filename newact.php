<?php

/*******w******** 
    
    Name: Brianne Coleman
    Date: January 30, 2023
    Description: WebDev 2 - Assignment 3 (Blog)

****************/

require('connect.php');
require('authenticate.php');

$stage_name = "";
$website = "";
$contact_phone = "";
$contact_email = "";

// Verifies that a post has occurred and a value exists.  The value is then sanitized to be used as a variable.

if($_POST && !empty($_POST['content']))
{
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}

if($_POST && !empty($_POST['title']))
{
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}

// Verifies that the length of both the content and the title variables is 1 or greater.

if(strlen($content) > 0 && strlen($title) > 0)
{
    // Adds the new post as a record in the database

    $query = "INSERT INTO blog_posts (title, content) VALUES (:title, :content)";

    $statement= $db->prepare($query);
    $statement->bindValue(':title', $title);
    $statement->bindValue(':content', $content);
    $statement->execute();

    header('Location: ./index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Create Post</title>
</head>
<body>

</body>
</html>