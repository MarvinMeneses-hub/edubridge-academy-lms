<?php

session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "teacher") {
    header("Location: ../login.php");
    exit();
}

$teacher_id = $_SESSION["user_id"];

$error = "";
$success = "";


/* Get teacher's courses */

$sql = "SELECT course_id, course_name
        FROM courses
        WHERE instructor_id = ?
        ORDER BY course_name ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();

$courses = $stmt->get_result();


/* Create announcement */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $course_id = $_POST["course_id"] ?? 0;
    $title = trim($_POST["title"] ?? "");
    $message = trim($_POST["message"] ?? "");


    if ($course_id <= 0 || $title == "" || $message == "") {

        $error = "Please complete all fields.";

    } else {

        /* Make sure the course belongs to this teacher */

        $sql = "SELECT course_id
                FROM courses
                WHERE course_id = ?
                AND instructor_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $course_id, $teacher_id);
        $stmt->execute();

        $result = $stmt->get_result();


        if ($result->num_rows != 1) {

            $error = "Invalid course.";

        } else {

            $sql = "INSERT INTO announcements
                    (course_id, teacher_id, title, message)
                    VALUES (?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "iiss",
                $course_id,
                $teacher_id,
                $title,
                $message
            );


            if ($stmt->execute()) {

                $success = "Announcement posted successfully.";

            } else {

                $error = "Failed to post announcement.";

            }
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Create Announcement - EduBridge LMS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<div class="header">

    <h1>EduBridge Academy LMS</h1>

    <p>Create Announcement</p>

</div>


<div class="container">

    <div class="card">

        <h2>Post Announcement</h2>

        <?php if ($success != ""): ?>

            <div class="success">
                <?php echo htmlspecialchars($success); ?>
            </div>

        <?php endif; ?>


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

                    <option value="<?php echo $course["course_id"]; ?>">

                        <?php echo htmlspecialchars($course["course_name"]); ?>

                    </option>

                <?php endwhile; ?>

            </select>


            <label>Announcement Title</label>

            <input
                type="text"
                name="title"
                placeholder="Enter announcement title"
                required
            >


            <label>Message</label>

            <textarea
                name="message"
                placeholder="Write your announcement here..."
                required
            ></textarea>


            <button type="submit">
                Post Announcement
            </button>

        </form>


        <a class="back" href="dashboard.php">
            ← Back to Teacher Dashboard
        </a>

    </div>

</div>

</body>

</html>