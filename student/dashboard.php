<?php
session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "student") {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$name = $_SESSION["name"];

/* Total Courses */
$sql = "SELECT COUNT(*) AS total FROM enrollments WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_courses = $stmt->get_result()->fetch_assoc()["total"];

/* Total Assignments */
$sql = "SELECT COUNT(*) AS total
        FROM assignments
        INNER JOIN enrollments
        ON assignments.course_id = enrollments.course_id
        WHERE enrollments.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_assignments = $stmt->get_result()->fetch_assoc()["total"];

/* Submitted */
$sql = "SELECT COUNT(*) AS total
        FROM submissions
        WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_submissions = $stmt->get_result()->fetch_assoc()["total"];

/* Graded */
$sql = "SELECT COUNT(*) AS total
        FROM submissions
        WHERE user_id = ?
        AND grade IS NOT NULL";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_graded = $stmt->get_result()->fetch_assoc()["total"];

/* Gamification */
$submission_points = $total_submissions * 10;
$grading_points = $total_graded * 5;
$total_points = $submission_points + $grading_points;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - EduBridge LMS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<div class="header">
    <h1>EduBridge Academy LMS</h1>
    <p>Welcome, <?php echo htmlspecialchars($name); ?>!</p>
    <a href="../logout.php">Logout</a>
</div>

<div class="container">

    <div class="card">
        <h2>Student Dashboard</h2>
        <p>View your courses, assignments, announcements, progress, and achievements.</p>
    </div>

    <div class="stats">

        <div class="stat">
            <p>My Courses</p>
            <h2><?php echo $total_courses; ?></h2>
        </div>

        <div class="stat">
            <p>Assignments</p>
            <h2><?php echo $total_assignments; ?></h2>
        </div>

        <div class="stat">
            <p>Submitted</p>
            <h2><?php echo $total_submissions; ?></h2>
        </div>

        <div class="stat">
            <p>Graded</p>
            <h2><?php echo $total_graded; ?></h2>
        </div>

    </div>

    <!-- ACHIEVEMENTS -->

    <div class="card">

        <h2>My Achievements</h2>

        <div class="achievement-box">

            <div class="achievement-stat">
                <p>Total Points</p>
                <h2><?php echo $total_points; ?></h2>
            </div>

            <div class="achievement-stat">
                <p>Assignments Submitted</p>
                <h2><?php echo $total_submissions; ?></h2>
            </div>

            <div class="achievement-stat">
                <p>Assignments Graded</p>
                <h2><?php echo $total_graded; ?></h2>
            </div>

        </div>

        <div class="badges">

            <div class="badge <?php echo $total_submissions >= 1 ? 'unlocked' : ''; ?>">
                <div class="badge-icon">🌸</div>
                <h3>Getting Started</h3>
                <p>Submit 1 assignment</p>
            </div>

            <div class="badge <?php echo $total_submissions >= 3 ? 'unlocked' : ''; ?>">
                <div class="badge-icon">📚</div>
                <h3>Active Learner</h3>
                <p>Submit 3 assignments</p>
            </div>

            <div class="badge <?php echo $total_submissions >= 5 ? 'unlocked' : ''; ?>">
                <div class="badge-icon">⭐</div>
                <h3>Dedicated Student</h3>
                <p>Submit 5 assignments</p>
            </div>

            <div class="badge <?php echo $total_graded >= 3 ? 'unlocked' : ''; ?>">
                <div class="badge-icon">🏆</div>
                <h3>Achievement Unlocked</h3>
                <p>Get 3 assignments graded</p>
            </div>

            <div class="badge <?php echo $total_graded >= 5 ? 'unlocked' : ''; ?>">
                <div class="badge-icon">💗</div>
                <h3>EduBridge Star</h3>
                <p>Get 5 assignments graded</p>
            </div>

        </div>

    </div>

    <!-- ANNOUNCEMENTS -->

    <div class="card">

        <h2>Announcements</h2>

        <?php

        $sql = "SELECT
                    announcements.title,
                    announcements.message,
                    announcements.created_at,
                    courses.course_name
                FROM announcements
                INNER JOIN courses
                ON announcements.course_id = courses.course_id
                INNER JOIN enrollments
                ON courses.course_id = enrollments.course_id
                WHERE enrollments.user_id = ?
                ORDER BY announcements.created_at DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            while ($announcement = $result->fetch_assoc()) {

                echo "<div class='announcement'>";

                echo "<h3>" .
                     htmlspecialchars($announcement["title"]) .
                     "</h3>";

                echo "<p><strong>Course:</strong> " .
                     htmlspecialchars($announcement["course_name"]) .
                     "</p>";

                echo "<p>" .
                     nl2br(htmlspecialchars($announcement["message"])) .
                     "</p>";

                echo "<small>Posted: " .
                     htmlspecialchars($announcement["created_at"]) .
                     "</small>";

                echo "</div>";
            }

        } else {

            echo "<p>No announcements yet.</p>";

        }

        ?>

    </div>

    <!-- AVAILABLE COURSES -->

    <div class="card">

        <h2>Available Courses</h2>

        <?php

        $sql = "SELECT * FROM courses ORDER BY course_name";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {

            while ($course = $result->fetch_assoc()) {

                echo "<div class='course'>";

                echo "<h3>" .
                     htmlspecialchars($course["course_name"]) .
                     "</h3>";

                echo "<form method='POST' action='enroll.php'>";

                echo "<input type='hidden'
                       name='course_id'
                       value='" . $course["course_id"] . "'>";

                echo "<button type='submit'>
                        Enroll
                      </button>";

                echo "</form>";

                echo "</div>";
            }

        } else {

            echo "<p>No courses available.</p>";

        }

        ?>

    </div>

    <!-- MY COURSES -->

    <div class="card">

        <h2>My Courses</h2>

        <?php

        $sql = "SELECT courses.course_id,
                       courses.course_name
                FROM enrollments
                INNER JOIN courses
                ON enrollments.course_id = courses.course_id
                WHERE enrollments.user_id = ?
                ORDER BY courses.course_name";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            while ($course = $result->fetch_assoc()) {

                $course_id = $course["course_id"];

                /* Total assignments */

                $sql_total = "SELECT COUNT(*) AS total
                              FROM assignments
                              WHERE course_id = ?";

                $stmt_total = $conn->prepare($sql_total);
                $stmt_total->bind_param("i", $course_id);
                $stmt_total->execute();

                $total =
                    $stmt_total->get_result()->fetch_assoc()["total"];

                /* Submitted */

                $sql_submitted = "SELECT COUNT(*) AS submitted
                                  FROM submissions
                                  INNER JOIN assignments
                                  ON submissions.assignment_id =
                                     assignments.assignment_id
                                  WHERE submissions.user_id = ?
                                  AND assignments.course_id = ?";

                $stmt_submitted = $conn->prepare($sql_submitted);

                $stmt_submitted->bind_param(
                    "ii",
                    $user_id,
                    $course_id
                );

                $stmt_submitted->execute();

                $submitted =
                    $stmt_submitted->get_result()->fetch_assoc()["submitted"];

                $progress = $total > 0
                    ? round(($submitted / $total) * 100)
                    : 0;

                echo "<div class='course enrolled'>";

                echo "<h3>" .
                     htmlspecialchars($course["course_name"]) .
                     "</h3>";

                echo "<p>You are enrolled in this course.</p>";

                echo "<p>
                        <strong>Progress:</strong>
                        $submitted / $total assignments completed
                      </p>";

                echo "<div class='progress-container'>";

                echo "<div class='progress-bar'
                           style='width:$progress%;'>
                      </div>";

                echo "</div>";

                echo "<p>$progress% Complete</p>";

                echo "<a href='course.php?course_id=" .
                     $course_id .
                     "' class='btn'>
                     View Course
                     </a>";

                echo "</div>";
            }

        } else {

            echo "<p>
                    You are not enrolled in any courses yet.
                  </p>";

        }

        ?>

    </div>

</div>

</body>
</html>