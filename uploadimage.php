<?php

/*******************
    
	Name: Brianne Coleman
	Date: April 2, 2023 
	Description: Final Project - Image Upload Page

*******************/
require('connect.php');
session_start();

$user_id = $_SESSION['user_id'];
$performer_id = $_SESSION['performer_id'];


 // file_upload_path() - Safely build a path String that uses slashes appropriate for our OS.

function file_upload_path($original_filename, $upload_subfolder_name = 'images')
{
    $current_folder = dirname(__FILE__);

    // Build an array of paths segment names to be joins using OS specific slashes.
    $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];

    // The DIRECTORY_SEPARATOR constant is OS agnostic.
    return join(DIRECTORY_SEPARATOR, $path_segments);
}

// file_is_an_image() - Checks the mime-type & extension of the uploaded file for "image-ness".
function file_is_an_image($temporary_path, $new_path)
{
    $allowed_mime_types = ['image/jpeg', 'image/png'];
    $allowed_file_extensions = ['jpg', 'jpeg', 'png'];

    $actual_file_extension = pathinfo($new_path, PATHINFO_EXTENSION);

    $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);

    return $file_extension_is_valid; 
}

$image_upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);
$upload_error_detected = isset($_FILES['image']) && ($_FILES['image']['error'] > 0);
$filetype_error = "File must be an image filetype (.jpeg, .jpg, or .png).";
$filetype_error_flag = false;
$actual_mime_type = "";

if($image_upload_detected)
{
    $image_filename = $_FILES['image']['name'];
    $temporary_image_path = $_FILES['image']['tmp_name'];
    $new_image_path = file_upload_path($image_filename);

    if(file_is_an_image($temporary_image_path, $new_image_path))
    {
        move_uploaded_file($temporary_image_path, $new_image_path);

        $query = "INSERT INTO images (filename, user_id) VALUES (:filename, :user_id)";

        $statement= $db->prepare($query);
        $statement->bindValue(':filename', $image_filename);
        $statement->bindValue(':user_id', $user_id);
        $statement->execute();
    }
    else 
    {
        $filetype_error_flag = true;
    }
}

?>

<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="main.css">
    <script type="text/javascript" src="script.js"></script>
    <title>Upload an Image</title>
</head>
<body>
    <h1>Upload an Image</h1>
    <a href="./index.php" class="nav">Home</a>&nbsp;&nbsp;|&nbsp;&nbsp;
    <a href="./profile.php?performer_id=<?= $performer_id ?>">Return to Profile</a>
    <br><br><br>
    <form method="post" enctype="multipart/form-data">
        <label for="image">Filename:</label>
        <input type="file" name="image" id="image">
        <input type="submit" name="submit" value="Upload Image">
    </form>
    <br>
    <?php if ($upload_error_detected): ?>
        <p>Error Number: <?= $_FILES['image']['error'] ?></p>
    <?php endif ?>
    <?php if($filetype_error_flag): ?>
        <p class="error"><?= $filetype_error ?></p>
    <?php elseif($_POST && !$upload_error_detected): ?>
        <p>Upload successful</p>
    <?php endif ?>
    
    
</body>
</html>

