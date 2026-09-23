<?php
session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
    exit();
}

$course_id = $_GET["id"] ?? 0;

if ($course_id <= 0) {
    die("Invalid course ID.");
}

/* Delete submissions related to assignments */

$sql = "DELETE FROM submissions
        WHERE assignment_id IN
        (
            SELECT assignment_id
            FROM assignments
            WHERE course_id = ?
        )";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $course_id
);

$stmt->execute();

/* Delete assignments */

$sql = "DELETE FROM assignments
        WHERE course_id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $course_id
);

$stmt->execute();

/* Delete enrollments */

$sql = "DELETE FROM enrollments
        WHERE course_id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $course_id
);

$stmt->execute();

/* Delete course */

$sql = "DELETE FROM courses
        WHERE course_id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $course_id
);

if ($stmt->execute()) {

    header("Location: courses.php");
    exit();

} else {

    die("Failed to delete course.");

}
?>