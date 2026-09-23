<?php
session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "teacher") {
    header("Location: ../login.php");
    exit();
}

$teacher_id = $_SESSION["user_id"];
$name = $_SESSION["name"];

/* Get teacher's courses */
$sql = "SELECT *
        FROM courses
        WHERE instructor_id = ?
        ORDER BY course_id ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$courses = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Teacher Dashboard - EduBridge LMS</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<!-- =====================================================
     HEADER
     ===================================================== -->

<div class="header">

    <h1>EduBridge Academy LMS</h1>

    <p>
        Welcome,
        <?php echo htmlspecialchars($name); ?>!
    </p>

    <a href="../logout.php" class="btn btn-back">
        Logout
    </a>

</div>


<!-- =====================================================
     MAIN CONTAINER
     ===================================================== -->

<div class="container">


    <!-- =================================================
         TEACHER DASHBOARD
         ================================================= -->

    <div class="card">

        <h2>Teacher Dashboard</h2>

        <p>
            Manage your courses and learning activities.
        </p>

        <div class="button-group">

            <a href="submissions.php" class="btn">
                View Student Submissions
            </a>

            <a href="create_announcement.php" class="btn">
                + Create Announcement
            </a>

            <a href="announcements.php" class="btn">
                Manage Announcements
            </a>

            <a href="analytics.php" class="btn">
                Analytics Dashboard
            </a>

        </div>

    </div>


    <!-- =================================================
         MY COURSES
         ================================================= -->

    <div class="card">

        <div class="button-group"
             style="justify-content: space-between;">

            <h2 style="margin: 0;">
                My Courses
            </h2>

            <a href="create_course.php" class="btn">
                + Create Course
            </a>

        </div>


        <?php if ($courses->num_rows > 0): ?>

            <?php while ($course = $courses->fetch_assoc()): ?>

                <div class="course">

                    <h3>
                        <?php
                        echo htmlspecialchars(
                            $course["course_name"]
                        );
                        ?>
                    </h3>

                    <p>
                        <strong>Course ID:</strong>
                        <?php
                        echo htmlspecialchars(
                            $course["course_id"]
                        );
                        ?>
                    </p>


                    <div class="button-group">

                        <a
                            href="create_assignment.php?course_id=<?php echo $course["course_id"]; ?>"
                            class="btn"
                        >
                            Create Assignment
                        </a>

                    </div>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="empty-state">

                <p>
                    You have not created any courses yet.
                </p>

                <a
                    href="create_course.php"
                    class="btn"
                >
                    + Create Your First Course
                </a>

            </div>

        <?php endif; ?>

    </div>


    <!-- =================================================
         QUICK ACTIONS
         ================================================= -->

    <div class="card">

        <h2>Quick Actions</h2>

        <div class="button-group">

            <a
                href="create_course.php"
                class="btn"
            >
                + Create Course
            </a>

            <a
                href="create_announcement.php"
                class="btn"
            >
                + Create Announcement
            </a>

            <a
                href="submissions.php"
                class="btn"
            >
                View Submissions
            </a>

            <a
                href="analytics.php"
                class="btn"
            >
                View Analytics
            </a>

        </div>

    </div>


</div>

</body>
</html>