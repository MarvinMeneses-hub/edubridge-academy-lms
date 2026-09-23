<?php
session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "teacher") {
    header("Location: ../login.php");
    exit();
}

$teacher_id = $_SESSION["user_id"];

/* Get teacher's assignments */
$sql = "SELECT
            assignments.assignment_id,
            assignments.title,
            assignments.description,
            assignments.due_date,
            courses.course_id,
            courses.course_name
        FROM assignments
        INNER JOIN courses
        ON assignments.course_id = courses.course_id
        WHERE courses.instructor_id = ?
        ORDER BY assignments.due_date ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>

<title>Manage Assignments - EduBridge LMS</title>

<link rel="stylesheet" href="../css/style.css">

</head>

<body>

<div class="header">

    <h1>EduBridge Academy LMS</h1>

    <p>
        Teacher Assignment Management
    </p>

    <div class="button-group">

        <a href="dashboard.php" class="btn">
            Dashboard
        </a>

        <a href="create_assignment.php" class="btn">
            + Create Assignment
        </a>

        <a href="../logout.php" class="btn btn-back">
            Logout
        </a>

    </div>

</div>


<div class="container">

    <div class="card">

        <h2>Manage Assignments</h2>

        <p>
            View, edit, or delete assignments from your courses.
        </p>

    </div>


    <?php if ($result->num_rows > 0): ?>

        <?php while ($assignment = $result->fetch_assoc()): ?>

            <div class="assignment">

                <h3>
                    <?php echo htmlspecialchars($assignment["title"]); ?>
                </h3>

                <p>
                    <strong>Course:</strong>
                    <?php echo htmlspecialchars($assignment["course_name"]); ?>
                </p>

                <p>
                    <strong>Description:</strong><br>
                    <?php echo nl2br(htmlspecialchars($assignment["description"])); ?>
                </p>

                <p>
                    <strong>Due Date:</strong>
                    <?php echo htmlspecialchars($assignment["due_date"]); ?>
                </p>


                <div class="button-group">

                    <a
                        href="edit_assignment.php?id=<?php echo $assignment["assignment_id"]; ?>"
                        class="btn"
                    >
                        Edit
                    </a>

                    <a
                        href="delete_assignment.php?id=<?php echo $assignment["assignment_id"]; ?>"
                        class="btn btn-danger"
                        onclick="return confirm('Are you sure you want to delete this assignment?');"
                    >
                        Delete
                    </a>

                </div>

            </div>

        <?php endwhile; ?>

    <?php else: ?>

        <div class="card">

            <p>
                No assignments found.
            </p>

        </div>

    <?php endif; ?>

</div>

</body>
</html>