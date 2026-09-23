<?php
session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "teacher") {
    header("Location: ../login.php");
    exit();
}

$teacher_id = $_SESSION["user_id"];

?>

<!DOCTYPE html>
<html>

<head>

    <title>Student Submissions - EduBridge LMS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<div class="header">

    <h1>EduBridge Academy LMS</h1>

    <a href="dashboard.php" style="color:white;">
        ← Teacher Dashboard
    </a>

</div>


<div class="container">

    <h2>Student Submissions</h2>


    <?php

    $sql = "SELECT
                submissions.submission_id,
                submissions.submission_text,
                submissions.submitted_at,
                submissions.grade,
                users.name AS student_name,
                assignments.title AS assignment_title,
                courses.course_name
            FROM submissions

            INNER JOIN users
                ON submissions.user_id = users.user_id

            INNER JOIN assignments
                ON submissions.assignment_id = assignments.assignment_id

            INNER JOIN courses
                ON assignments.course_id = courses.course_id

            WHERE courses.instructor_id = ?

            ORDER BY submissions.submitted_at DESC";


    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "i",
        $teacher_id
    );

    $stmt->execute();

    $result = $stmt->get_result();


    if ($result->num_rows > 0) {

        while ($submission = $result->fetch_assoc()) {

            echo "<div class='submission'>";

            echo "<h3>" .
                htmlspecialchars($submission["assignment_title"]) .
                "</h3>";

            echo "<p><strong>Course:</strong> " .
                htmlspecialchars($submission["course_name"]) .
                "</p>";

            echo "<p><strong>Student:</strong> " .
                htmlspecialchars($submission["student_name"]) .
                "</p>";

            echo "<p><strong>Submitted:</strong> " .
                htmlspecialchars($submission["submitted_at"]) .
                "</p>";

            echo "<hr>";

            echo "<p><strong>Submission:</strong></p>";

            echo "<p>" .
                nl2br(
                    htmlspecialchars(
                        $submission["submission_text"]
                    )
                ) .
                "</p>";


            if ($submission["grade"] === null) {

                echo "<p><strong>Status:</strong> Not graded</p>";

            } else {

                echo "<p><strong>Grade:</strong> " .
                    htmlspecialchars($submission["grade"]) .
                    "</p>";

            }


            echo "<a class='button'
                    href='grade_submission.php?id=" .
                    $submission["submission_id"] .
                    "'>";

            echo "Grade Submission";

            echo "</a>";

            echo "</div>";
        }

    } else {

        echo "<p>No student submissions yet.</p>";

    }

    ?>

</div>

</body>

</html>