<?php

/**************** 
    
    Name: Brianne Coleman
    Date: March 21, 2023
    Description: WebDev 2 Final Project: Edit/Delete performer profile

****************/

require('connect.php');
session_start();

$user_id = $_SESSION['user_id'];
$performer_id = $_SESSION['performer_id'];

/**********************************
    Performer Information Form 
**********************************/

// Variables

$error_flag = false;
$error_message = "Contact details cannot be empty.";

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

            header('Location: ./edit.php?performer_id={$performer_id}');
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

/**********************************
    Upload Image Form 
**********************************/

// Variables

$image_upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);
$upload_error_detected = isset($_FILES['image']) && ($_FILES['image']['error'] > 0);
$filetype_error = "File must be an image filetype (.jpeg, .jpg, or .png).";
$filetype_error_flag = false;

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

// Retrieves the images from the database that match the user_id, stores them in an array

$query = "SELECT * FROM images WHERE user_id = $user_id";

$image_statement = $db->prepare($query);
$image_statement->execute();

$images = [];

while($row = $image_statement->fetch())
{
    $images[] = $row;
}

// Resizing the images for display

for($i=0; $i < count($images); $i++)
{
    $source_img = "images/" . $images[$i]['filename'];
    $destination_img = "images/resized/" . $images[$i]['filename'];

    $size = getimagesize($source_img);
    $width = $size[0];
    $height = $size[1];

    $resize = "0.25";
    $rwidth = ceil($width * $resize);
    $rheight = ceil($height * $resize);

    if(substr($source_img, -3) == "png")
    {
        $original = imagecreatefrompng($source_img);

        $resized = imagecreatetruecolor($rwidth, $rheight);
        imagecopyresampled($resized, $original, 0, 0, 0, 0, $rwidth, $rheight, $width, $height);

        imagepng($resized, $destination_img);
    }
    else 
    {
        $original = imagecreatefromjpeg($source_img);

        $resized = imagecreatetruecolor($rwidth, $rheight);
        imagecopyresampled($resized, $original, 0, 0, 0, 0, $rwidth, $rheight, $width, $height);

        imagejpeg($resized, $destination_img);
    }
  
    $resized_images[] = $destination_img;
}

if(isset($_POST['img_delete']))
{
    if(isset($_POST['checkbox']))
    {
        foreach($_POST['checkbox'] as $filename)
        {
            //delete file from resized folder
            unlink($filename);    

            $index = strripos($filename, '/');
            $filename = substr($filename, $index + 1);

            //delete file from image folder
            unlink("images/" . $filename);

            $query = "DELETE FROM images WHERE filename = :filename";

            $statement = $db->prepare($query);
            $statement->bindValue(':filename', $filename);
            $statement->execute();
        }
    }

    header('Location: ./profile.php?performer_id={$performer_id}');
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
        <a href="./newact.php?performer_id=<?= $profile['performer_id'] ?>">Add Act Information</a>&nbsp;&nbsp;|&nbsp;&nbsp;
    </div>
    <br>
    <form method="post">
        <input type="hidden" name="performer_id" value="<?= $profile['performer_id'] ?>">
        <?php if(!$error_flag): ?>
            <h3>Performer Information</h3>
            <br>
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
    <br><br>
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
        <?php if(count($images) > 0): ?>
            <form method="post">
                <ul>
                    <?php foreach ($resized_images as $resized_image): ?>
                        <li><input type="checkbox" value="<?php echo $resized_image; ?>" name="checkbox[]"><img src = "<?= $resized_image ?>"></li>
                    <?php endforeach ?> 
                </ul>
                <input type="submit" id="img_delete" value="Delete Image(s)" name="img_delete" onclick="return confirmDeleteImg()">
            </form>
        <?php endif ?>
</body>
</html>