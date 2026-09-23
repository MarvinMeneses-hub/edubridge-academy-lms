<?php
session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
    exit();
}

$user_id = $_GET["id"] ?? 0;


// Prevent admin from deleting their own account
if ($user_id == $_SESSION["user_id"]) {
    die("You cannot delete your own admin account.");
}


// Check if user exists
$sql = "SELECT user_id FROM users WHERE user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows != 1) {
    die("User not found.");
}


// Delete user
$sql = "DELETE FROM users WHERE user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {

    header("Location: users.php");
    exit();

} else {

    die("Unable to delete user. The user may still have related records.");
}
?>