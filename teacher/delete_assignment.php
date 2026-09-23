<?php
session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "teacher") {
    header("Location: ../login.php");
    exit();
}

$teacher_id = $_SESSION["user_id"];
$assignment_id = $_GET["id"] ?? 0;

if ($assignment_id <= 0) {
    die("Invalid assignment ID.");
}


/* Verify assignment belongs to teacher */

$sql = "SELECT assignments.assignment_id
        FROM assignments
        INNER JOIN courses
        ON assignments.course_id = courses.course_id
        WHERE assignments.assignment_id = ?
        AND courses.instructor_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $assignment_id, $teacher_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Assignment not found or you do not have permission to delete it.");
}


/* Delete submissions first */

$sql = "DELETE FROM submissions
        WHERE assignment_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $assignment_id);
$stmt->execute();


/* Delete assignment */

$sql = "DELETE FROM assignments
        WHERE assignment_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $assignment_id);

if ($stmt->execute()) {

    header("Location: assignments.php");
    exit();

} else {

    die("Failed to delete assignment.");

}
?>