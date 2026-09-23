<?php
session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
    exit();
}

$name = $_SESSION["name"];

/* Total Users */

$sql = "SELECT COUNT(*) AS total FROM users";
$result = $conn->query($sql);
$total_users = $result->fetch_assoc()["total"];

/* Students */

$sql = "SELECT COUNT(*) AS total
        FROM users
        WHERE role = 'student'";
$result = $conn->query($sql);
$total_students = $result->fetch_assoc()["total"];

/* Teachers */

$sql = "SELECT COUNT(*) AS total
        FROM users
        WHERE role = 'teacher'";
$result = $conn->query($sql);
$total_teachers = $result->fetch_assoc()["total"];

/* Courses */

$sql = "SELECT COUNT(*) AS total FROM courses";
$result = $conn->query($sql);
$total_courses = $result->fetch_assoc()["total"];

?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard - EduBridge LMS</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<div class="header">

    <h1>EduBridge Academy LMS</h1>

    <p>
        Welcome, <?php echo htmlspecialchars($name); ?>!
    </p>

    <a href="../logout.php">
        Logout
    </a>

</div>

<div class="container">

    <div class="card">

        <h2>Administrator Dashboard</h2>

        <p>
            Manage users, teachers, students, and courses.
        </p>

    </div>


    <div class="stats">

        <div class="stat">

            <p>Total Users</p>

            <h2>
                <?php echo $total_users; ?>
            </h2>

        </div>


        <div class="stat">

            <p>Students</p>

            <h2>
                <?php echo $total_students; ?>
            </h2>

        </div>


        <div class="stat">

            <p>Teachers</p>

            <h2>
                <?php echo $total_teachers; ?>
            </h2>

        </div>


        <div class="stat">

            <p>Courses</p>

            <h2>
                <?php echo $total_courses; ?>
            </h2>

        </div>

    </div>


    <div class="card">

        <h2>Administration</h2>

        <div class="button-group">

            <a href="users.php"
               class="btn">
                Manage Users
            </a>

            <a href="courses.php"
               class="btn">
                Manage Courses
            </a>

        </div>

    </div>

</div>

</body>
</html>