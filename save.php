<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $email      = $_POST['email'];

    $profile_picture = "";

    // Handle file upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $targetDir = "uploads/";
        $fileName = time() . "_" . basename($_FILES["profile_picture"]["name"]);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFile)) {
            $profile_picture = $fileName;
        }
    }

    $sql = "INSERT INTO registration (first_name, last_name, email, profile_picture) 
            VALUES ('$first_name', '$last_name', '$email', '$profile_picture')";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
