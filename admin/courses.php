<?php
session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
    exit();
}

$sql = "SELECT
            courses.course_id,
            courses.course_name,
            users.name AS instructor_name

        FROM courses

        INNER JOIN users
        ON courses.instructor_id = users.user_id

        ORDER BY courses.course_name";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manage Courses - EduBridge LMS</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<div class="header">
    <h1>EduBridge Academy LMS</h1>
</div>

<div class="container">

    <div class="card">

        <h2>Manage Courses</h2>

        <div class="button-group">

            <a href="add_course.php"
               class="btn">
                + Add Course
            </a>

        </div>

        <br>

        <table>

            <tr>

                <th>ID</th>

                <th>Course</th>

                <th>Instructor</th>

                <th>Actions</th>

            </tr>

            <?php while ($course = $result->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?php echo $course["course_id"]; ?>
                    </td>

                    <td>
                        <?php
                        echo htmlspecialchars(
                            $course["course_name"]
                        );
                        ?>
                    </td>

                    <td>
                        <?php
                        echo htmlspecialchars(
                            $course["instructor_name"]
                        );
                        ?>
                    </td>

                    <td>

                        <a
                            href="edit_course.php?id=<?php echo $course["course_id"]; ?>"
                            class="btn">
                            Edit
                        </a>

                        <a
                            href="delete_course.php?id=<?php echo $course["course_id"]; ?>"
                            class="btn btn-danger"
                            onclick="return confirm('Delete this course and its related records?');">
                            Delete
                        </a>

                    </td>

                </tr>

            <?php endwhile; ?>

        </table>

        <div class="button-group">

            <a href="dashboard.php"
               class="btn btn-back">
                ← Dashboard
            </a>

        </div>

    </div>

</div>

</body>
</html>