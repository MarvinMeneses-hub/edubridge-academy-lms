<?php

session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "teacher") {
    header("Location: ../login.php");
    exit();
}

$teacher_id = $_SESSION["user_id"];

?>

<!DOCTYPE html>
<html>

<head>

    <title>Manage Announcements - EduBridge LMS</title>
   <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<div class="header">

    <h1>EduBridge Academy LMS</h1>

    <p>Manage Announcements</p>

</div>


<div class="container">

    <div class="card">

        <h2>Announcements</h2>

        <a class="button create" href="create_announcement.php">
            + Create Announcement
        </a>

        <br><br>

        <?php

        $sql = "SELECT
                    announcements.announcement_id,
                    announcements.title,
                    announcements.message,
                    announcements.created_at,
                    courses.course_name
                FROM announcements
                INNER JOIN courses
                ON announcements.course_id = courses.course_id
                WHERE announcements.teacher_id = ?
                ORDER BY announcements.created_at DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $teacher_id);
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

                echo "<br><br>";

                echo "<a class='button edit'
                         href='edit_announcement.php?id=" .
                         $announcement["announcement_id"] .
                         "'>
                         Edit
                      </a>";

                echo "<a class='button delete'
                         href='delete_announcement.php?id=" .
                         $announcement["announcement_id"] .
                         "'
                         onclick=\"return confirm('Are you sure you want to delete this announcement?');\">
                         Delete
                      </a>";

                echo "</div>";
            }

        } else {

            echo "<p>No announcements have been posted yet.</p>";

        }

        ?>

        <br>

        <a class="back" href="dashboard.php">
            ← Back to Teacher Dashboard
        </a>

    </div>

</div>

</body>

</html>