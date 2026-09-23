<?php
session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "teacher") {
    header("Location: ../login.php");
    exit();
}

$teacher_id = $_SESSION["user_id"];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $course_name = trim($_POST["course_name"]);

    if ($course_name != "") {

        $sql = "INSERT INTO courses (course_name, instructor_id)
                VALUES (?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "si",
            $course_name,
            $teacher_id
        );

        if ($stmt->execute()) {

            $message = "Course created successfully!";

        } else {

            $message = "Error creating course.";

        }

    } else {

        $message = "Please enter a course name.";

    }
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Create Course - EduBridge LMS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<div class="container">

    <h2>Create New Course</h2>

    <?php if ($message != ""): ?>

        <p class="message">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>


    <form method="POST">

        <label>Course Name</label>

        <input
            type="text"
            name="course_name"
            placeholder="Enter course name"
            required
        >

        <button type="submit">
            Create Course
        </button>

    </form>

    <br>

    <a href="dashboard.php">
        ← Back to Dashboard
    </a>

</div>

</body>
</html>