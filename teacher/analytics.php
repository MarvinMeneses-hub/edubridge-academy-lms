<?php
session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "teacher") {
    header("Location: ../login.php");
    exit();
}

$teacher_id = $_SESSION["user_id"];

/* Total students */
$sql = "SELECT COUNT(DISTINCT enrollments.user_id) AS total
        FROM enrollments
        INNER JOIN courses
        ON enrollments.course_id = courses.course_id
        WHERE courses.instructor_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$total_students = $stmt->get_result()->fetch_assoc()["total"];


/* Total assignments */
$sql = "SELECT COUNT(*) AS total
        FROM assignments
        INNER JOIN courses
        ON assignments.course_id = courses.course_id
        WHERE courses.instructor_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$total_assignments = $stmt->get_result()->fetch_assoc()["total"];


/* Total submissions */
$sql = "SELECT COUNT(*) AS total
        FROM submissions
        INNER JOIN assignments
        ON submissions.assignment_id = assignments.assignment_id
        INNER JOIN courses
        ON assignments.course_id = courses.course_id
        WHERE courses.instructor_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$total_submissions = $stmt->get_result()->fetch_assoc()["total"];


/* Graded submissions */
$sql = "SELECT COUNT(*) AS total
        FROM submissions
        INNER JOIN assignments
        ON submissions.assignment_id = assignments.assignment_id
        INNER JOIN courses
        ON assignments.course_id = courses.course_id
        WHERE courses.instructor_id = ?
        AND submissions.grade IS NOT NULL";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$graded_submissions = $stmt->get_result()->fetch_assoc()["total"];


/* Average grade */
$sql = "SELECT AVG(submissions.grade) AS average_grade
        FROM submissions
        INNER JOIN assignments
        ON submissions.assignment_id = assignments.assignment_id
        INNER JOIN courses
        ON assignments.course_id = courses.course_id
        WHERE courses.instructor_id = ?
        AND submissions.grade IS NOT NULL";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();

$average_result = $stmt->get_result()->fetch_assoc();

$average_grade = $average_result["average_grade"];

if ($average_grade !== null) {
    $average_grade = round($average_grade, 2);
} else {
    $average_grade = 0;
}


/* Course statistics */
$sql = "SELECT
            courses.course_name,
            COUNT(DISTINCT enrollments.user_id) AS students,
            COUNT(DISTINCT assignments.assignment_id) AS assignments
        FROM courses
        LEFT JOIN enrollments
        ON courses.course_id = enrollments.course_id
        LEFT JOIN assignments
        ON courses.course_id = assignments.course_id
        WHERE courses.instructor_id = ?
        GROUP BY courses.course_id
        ORDER BY courses.course_name";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();

$courses = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>

    <title>Analytics - EduBridge LMS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<div class="container">

    <a href="dashboard.php" class="back">
        ← Back to Dashboard
    </a>

    <div class="card">

        <h1>Teacher Analytics Dashboard</h1>

        <p>
            Overview of your courses, students, assignments, and grades.
        </p>

    </div>


    <!-- Statistics -->

    <div class="stats">

        <div class="stat">

            <h3>Total Students</h3>

            <p>
                <?php echo $total_students; ?>
            </p>

        </div>


        <div class="stat">

            <h3>Total Assignments</h3>

            <p>
                <?php echo $total_assignments; ?>
            </p>

        </div>


        <div class="stat">

            <h3>Total Submissions</h3>

            <p>
                <?php echo $total_submissions; ?>
            </p>

        </div>


        <div class="stat">

            <h3>Graded Submissions</h3>

            <p>
                <?php echo $graded_submissions; ?>
            </p>

        </div>


        <div class="stat">

            <h3>Average Grade</h3>

            <p>
                <?php echo $average_grade; ?>%
            </p>

        </div>

    </div>


    <!-- Course Statistics -->

    <div class="card">

        <h2>Course Statistics</h2>

        <?php if ($courses->num_rows > 0): ?>

            <table>

                <tr>
                    <th>Course</th>
                    <th>Students</th>
                    <th>Assignments</th>
                </tr>

                <?php while ($course = $courses->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $course["course_name"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php echo $course["students"]; ?>
                        </td>

                        <td>
                            <?php echo $course["assignments"]; ?>
                        </td>

                    </tr>

                <?php endwhile; ?>

            </table>

        <?php else: ?>

            <p>No course data available.</p>

        <?php endif; ?>

    </div>

</div>

</body>

</html>