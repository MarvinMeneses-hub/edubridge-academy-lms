<?php

session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "teacher") {
    header("Location: ../login.php");
    exit();
}

$teacher_id = $_SESSION["user_id"];
$announcement_id = $_GET["id"] ?? 0;

if ($announcement_id <= 0) {
    die("Invalid announcement ID.");
}


/* Delete only if the announcement belongs to this teacher */

$sql = "DELETE FROM announcements
        WHERE announcement_id = ?
        AND teacher_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ii",
    $announcement_id,
    $teacher_id
);

if ($stmt->execute()) {

    header("Location: announcements.php");
    exit();

} else {

    die("Failed to delete announcement: " . $conn->error);

}

?>