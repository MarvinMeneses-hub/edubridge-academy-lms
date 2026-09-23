<?php
session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "student") {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$course_id = $_GET["course_id"] ?? $_GET["id"] ?? 0;

if (!$course_id) {
    die("No course selected.");
}

/* Check enrollment */

$sql = "SELECT courses.*
        FROM courses
        INNER JOIN enrollments
        ON courses.course_id = enrollments.course_id
        WHERE courses.course_id = ?
        AND enrollments.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $course_id, $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("You are not enrolled in this course.");
}

$course = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($course["course_name"]); ?>
        - EduBridge LMS
    </title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<div class="header">

    <h1>EduBridge Academy LMS</h1>

    <p>
        <?php echo htmlspecialchars($course["course_name"]); ?>
    </p>

    <a href="dashboard.php">
        Dashboard
    </a>

</div>

<div class="container">

    <div class="card">

        <h2>
            <?php echo htmlspecialchars($course["course_name"]); ?>
        </h2>

        <p>
            View the assignments and learning activities
            for this course.
        </p>

    </div>

    <div class="card">

        <h2>Assignments</h2>

        <?php

        $sql = "SELECT
                    assignments.*,
                    submissions.submission_id,
                    submissions.grade

                FROM assignments

                LEFT JOIN submissions
                ON assignments.assignment_id =
                   submissions.assignment_id

                AND submissions.user_id = ?

                WHERE assignments.course_id = ?

                ORDER BY assignments.due_date ASC";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "ii",
            $user_id,
            $course_id
        );

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            while ($assignment = $result->fetch_assoc()) {

                echo "<div class='assignment'>";

                echo "<h3>" .
                     htmlspecialchars($assignment["title"]) .
                     "</h3>";

                echo "<p>" .
                     nl2br(
                         htmlspecialchars(
                             $assignment["description"]
                         )
                     ) .
                     "</p>";

                echo "<p>
                        <strong>Due Date:</strong>
                      " .
                     htmlspecialchars(
                         $assignment["due_date"]
                     ) .
                     "</p>";

                if ($assignment["submission_id"]) {

                    echo "<p class='status submitted'>
                            ✓ Submitted
                          </p>";

                    if ($assignment["grade"] !== null) {

                        echo "<p>
                                <strong>Grade:</strong> " .
                             htmlspecialchars(
                                 $assignment["grade"]
                             ) .
                             "
                              </p>";

                    } else {

                        echo "<p>
                                Waiting for teacher to grade.
                              </p>";

                    }

                } else {

                    echo "<p class='status not-submitted'>
                            Not Submitted
                          </p>";

                    echo "<a
                            href='submit_assignment.php?assignment_id=" .
                            $assignment["assignment_id"] .
                            "'
                            class='btn'>
                            Submit Assignment
                          </a>";
                }

                echo "</div>";
            }

        } else {

            echo "<p>
                    No assignments available for this course.
                  </p>";

        }

        ?>

        <div class="button-group">

            <a href="dashboard.php"
               class="btn btn-back">
                ← Back to Dashboard
            </a>

        </div>

    </div>

</div>

</body>
</html>