<?php
require 'db.php';


function uploadPicture($file) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    // allowed extensions
    $allowed = ['jpg','jpeg','png','gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        return null; // silently ignore invalid type (you can handle error messages as needed)
    }

   
    if ($file['size'] > 2 * 1024 * 1024) {
        return null;
    }

    $filename = uniqid('pic_', true) . '.' . $ext;
    $destination = __DIR__ . '/uploads/' . $filename;

    if (!is_dir(__DIR__ . '/uploads')) {
        mkdir(__DIR__ . '/uploads', 0755, true);
    }

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $filename;
    }
    return null;
}

/* --------------------------
   DELETE
   -------------------------- */
if (isset($_POST['delete_id'])) {
    $id = (int) $_POST['delete_id'];

    // fetch current picture to delete file
    $stmt = $mysqli->prepare("SELECT picture FROM registration WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();

    if ($row && !empty($row['picture'])) {
        $filePath = __DIR__ . '/uploads/' . $row['picture'];
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    $stmt = $mysqli->prepare("DELETE FROM registration WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header('Location: index.php');
    exit;
}

/* --------------------------
   UPDATE
   -------------------------- */
if (isset($_POST['id'])) {
    $id = (int) $_POST['id'];
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName  = trim($_POST['lastName'] ?? '');
    $dob       = trim($_POST['dob'] ?? '');
    $gender    = trim($_POST['gender'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? ''; // optional on update
    $phone     = trim($_POST['number'] ?? '');

    // basic validation (all except password required)
    if ($firstName === '' || $lastName === '' || $dob === '' || $gender === '' || $email === '' || $phone === '') {
        header('Location: index.php?err=' . urlencode('All fields except password are required for update.'));
        exit;
    }

    // handle new picture upload (if any)
    $newPic = uploadPicture($_FILES['picture'] ?? null);

    // if new picture uploaded, delete old file after fetching old name
    if ($newPic) {
        $stmt = $mysqli->prepare("SELECT picture FROM registration WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $old = $res->fetch_assoc();
        $stmt->close();
        if ($old && !empty($old['picture'])) {
            $oldPath = __DIR__ . '/uploads/' . $old['picture'];
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }
    }

    // build UPDATE statement depending on whether password and/or picture provided
    if ($password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        if ($newPic) {
            $stmt = $mysqli->prepare("UPDATE registration SET firstName=?, lastName=?, dob=?, gender=?, email=?, password_hash=?, phone=?, picture=?, updated_at=NOW() WHERE id=?");
            $stmt->bind_param("ssssssssi", $firstName, $lastName, $dob, $gender, $email, $hash, $phone, $newPic, $id);
        } else {
            $stmt = $mysqli->prepare("UPDATE registration SET firstName=?, lastName=?, dob=?, gender=?, email=?, password_hash=?, phone=?, updated_at=NOW() WHERE id=?");
            $stmt->bind_param("sssssssi", $firstName, $lastName, $dob, $gender, $email, $hash, $phone, $id);
        }
    } else {
        // no password change
        if ($newPic) {
            $stmt = $mysqli->prepare("UPDATE registration SET firstName=?, lastName=?, dob=?, gender=?, email=?, phone=?, picture=?, updated_at=NOW() WHERE id=?");
            $stmt->bind_param("sssssssi", $firstName, $lastName, $dob, $gender, $email, $phone, $newPic, $id);
        } else {
            $stmt = $mysqli->prepare("UPDATE registration SET firstName=?, lastName=?, dob=?, gender=?, email=?, phone=?, updated_at=NOW() WHERE id=?");
            $stmt->bind_param("ssssssi", $firstName, $lastName, $dob, $gender, $email, $phone, $id);
        }
    }

    $stmt->execute();
    $stmt->close();

    header('Location: index.php?ok=1');
    exit;
}

/* --------------------------
   INSERT
   -------------------------- */
$firstName = trim($_POST['firstName'] ?? '');
$lastName  = trim($_POST['lastName'] ?? '');
$dob       = trim($_POST['dob'] ?? '');
$gender    = trim($_POST['gender'] ?? '');
$email     = trim($_POST['email'] ?? '');
$password  = $_POST['password'] ?? '';
$phone     = trim($_POST['number'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // handle new registration: require password
    if ($firstName === '' || $lastName === '' || $dob === '' || $gender === '' || $email === '' || $password === '' || $phone === '') {
        header('Location: index.php?err=' . urlencode('All fields are required for new registration.'));
        exit;
    }

    // upload picture (optional)
    $pic = uploadPicture($_FILES['picture'] ?? null);

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare("INSERT INTO registration (firstName, lastName, dob, gender, email, password_hash, phone, picture, created_at) VALUES (?,?,?,?,?,?,?,?,NOW())");
    $stmt->bind_param("ssssssss", $firstName, $lastName, $dob, $gender, $email, $hash, $phone, $pic);
    $stmt->execute();
    $stmt->close();

    header('Location: index.php?ok=1');
    exit;
}

// If not POST (shouldn't happen), redirect
header('Location: index.php');
exit;
