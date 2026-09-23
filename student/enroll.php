<?php
session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "student") {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$course_id = $_POST["course_id"] ?? 0;

if ($course_id <= 0) {
    header("Location: dashboard.php");
    exit();
}

/* Check if course exists */
$sql = "SELECT course_id FROM courses WHERE course_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Course not found.");
}

/* Check if already enrolled */
$sql = "SELECT enrollment_id
        FROM enrollments
        WHERE user_id = ?
        AND course_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $course_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    header("Location: dashboard.php");
    exit();
}

/* Enroll student */
$sql = "INSERT INTO enrollments (user_id, course_id)
        VALUES (?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $course_id);

if ($stmt->execute()) {
    header("Location: dashboard.php");
    exit();
} else {
    die("Error enrolling in course.");
}
?>