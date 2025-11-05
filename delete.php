<?php
include "db.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];


    $result = $conn->query("SELECT profile_picture FROM registration WHERE id=$id");
    $row = $result->fetch_assoc();

    // Delete file if exists
    if ($row['profile_picture'] && file_exists("uploads/" . $row['profile_picture'])) {
        unlink("uploads/" . $row['profile_picture']);
    }

    // Delete record from DB
    $conn->query("DELETE FROM registration WHERE id=$id");

    header("Location: index.php");
}
?>
