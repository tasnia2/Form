<?php

require __DIR__ . '/db.php';

$firstName = trim($_POST['firstName'] ?? '');
$lastName  = trim($_POST['lastName']  ?? '');
$dob = trim($_POST['dob'] ?? '');
$gender    = trim($_POST['gender']    ?? '');
$email     = trim($_POST['email']     ?? '');
$password  = $_POST['password']       ?? '';
$number    = trim($_POST['number']    ?? '');


if ($firstName === '' || $lastName === '' || $gender === '' || $email === '' || $password === '' || $number === '' || $dob === '') {
    header('Location: index.php?err=' . urlencode('All fields are required.'));
    exit;
}


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: index.php?err=' . urlencode('Invalid email address.'));
    exit;
}

if (!in_array($gender, ['male','female','other'], true)) {
    header('Location: index.php?err=' . urlencode('Invalid gender value.'));
    exit;
}


$hash = password_hash($password, PASSWORD_DEFAULT);



try {

    $stmt = $mysqli->prepare(
    "INSERT INTO registration (firstName, lastName, gender, email, password_hash, phone, dob)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param("sssssss", $firstName, $lastName, $gender, $email, $hash, $number, $dob);
$stmt->execute();
$stmt->close();


    header('Location: index.php?ok=1');
    exit;

} catch (mysqli_sql_exception $e) {

    if ($mysqli->errno === 1062) {
        $msg = 'This email is already registered.';
    } else {
        $msg = 'Database error: ' . $e->getMessage();
    }
    header('Location: index.php?err=' . urlencode($msg));
    exit;
}