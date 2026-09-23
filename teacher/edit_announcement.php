<?php

session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "teacher") {
    header("Location: ../login.php");
    exit();
}

$teacher_id = $_SESSION["user_id"];
$announcement_id = $_GET["id"] ?? 0;

if ($announcement_id <= 0) {
    die("Invalid announcement ID.");
}


/* Get announcement */

$sql = "SELECT *
        FROM announcements
        WHERE announcement_id = ?
        AND teacher_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $announcement_id, $teacher_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows != 1) {
    die("Announcement not found.");
}

$announcement = $result->fetch_assoc();


/* Get teacher's courses */

$sql = "SELECT course_id, course_name
        FROM courses
        WHERE instructor_id = ?
        ORDER BY course_name ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();

$courses = $stmt->get_result();


$error = "";
$success = "";


/* Update announcement */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $course_id = $_POST["course_id"] ?? 0;
    $title = trim($_POST["title"] ?? "");
    $message = trim($_POST["message"] ?? "");


    if ($course_id <= 0 || $title == "" || $message == "") {

        $error = "Please complete all fields.";

    } else {

        /* Check that selected course belongs to teacher */

        $sql = "SELECT course_id
                FROM courses
                WHERE course_id = ?
                AND instructor_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $course_id, $teacher_id);
        $stmt->execute();

        $check = $stmt->get_result();


        if ($check->num_rows != 1) {

            $error = "Invalid course.";

        } else {

            $sql = "UPDATE announcements
                    SET course_id = ?,
                        title = ?,
                        message = ?
                    WHERE announcement_id = ?
                    AND teacher_id = ?";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "issii",
                $course_id,
                $title,
                $message,
                $announcement_id,
                $teacher_id
            );


            if ($stmt->execute()) {

                header("Location: announcements.php");
                exit();

            } else {

                $error = "Failed to update announcement.";

            }
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Edit Announcement - EduBridge LMS</title>
   <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<div class="header">

    <h1>EduBridge Academy LMS</h1>

    <p>Edit Announcement</p>

</div>


<div class="container">

    <div class="card">

        <h2>Edit Announcement</h2>


        <?php if ($error != ""): ?>

            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>


        <form method="POST">

            <label>Course</label>

            <select name="course_id" required>

                <option value="">Select Course</option>

                <?php while ($course = $courses->fetch_assoc()): ?>

                    <option
                        value="<?php echo $course["course_id"]; ?>"
                        <?php
                        if ($course["course_id"] == $announcement["course_id"]) {
                            echo "selected";
                        }
                        ?>
                    >

                        <?php echo htmlspecialchars($course["course_name"]); ?>

                    </option>

                <?php endwhile; ?>

            </select>


            <label>Announcement Title</label>

            <input
                type="text"
                name="title"
                value="<?php echo htmlspecialchars($announcement["title"]); ?>"
                required
            >


            <label>Message</label>

            <textarea
                name="message"
                required
            ><?php echo htmlspecialchars($announcement["message"]); ?></textarea>


            <button type="submit">
                Update Announcement
            </button>

        </form>


        <a class="back" href="announcements.php">
            ← Back to Announcements
        </a>

    </div>

</div>

</body>

</html>