<?php
session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "teacher") {
    header("Location: ../login.php");
    exit();
}

$teacher_id = $_SESSION["user_id"];
$course_id = $_GET["course_id"] ?? null;
$message = "";

// Check if course belongs to this teacher
if (!$course_id) {
    die("No course selected.");
}

$sql = "SELECT * FROM courses
        WHERE course_id = ? AND instructor_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $course_id, $teacher_id);
$stmt->execute();

$course_result = $stmt->get_result();

if ($course_result->num_rows == 0) {
    die("You do not have permission to manage this course.");
}

$course = $course_result->fetch_assoc();


// Create assignment
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $due_date = $_POST["due_date"];

    if ($title != "" && $description != "" && $due_date != "") {

        $sql = "INSERT INTO assignments
                (course_id, title, description, due_date)
                VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "isss",
            $course_id,
            $title,
            $description,
            $due_date
        );

        if ($stmt->execute()) {

            $message = "Assignment created successfully!";

        } else {

            $message = "Error creating assignment.";

        }

    } else {

        $message = "Please complete all fields.";

    }
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>Create Assignment - EduBridge LMS</title>
   <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<div class="container">

    <h2>Create Assignment</h2>

    <h3>
        Course:
        <?php echo htmlspecialchars($course["course_name"]); ?>
    </h3>


    <?php if ($message != ""): ?>

        <div class="message">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <form method="POST">

        <label>Assignment Title</label>

        <input
            type="text"
            name="title"
            placeholder="Example: Create Context Diagram"
            required
        >


        <label>Description</label>

        <textarea
            name="description"
            placeholder="Enter assignment instructions..."
            required
        ></textarea>


        <label>Due Date</label>

        <input
            type="date"
            name="due_date"
            required
        >


        <button type="submit">
            Create Assignment
        </button>

    </form>

    <br>

    <a href="dashboard.php">
        ← Back to Teacher Dashboard
    </a>
    <a href="assignments.php"
   style="display:inline-block;
          background:#2c3e50;
          color:white;
          padding:10px 15px;
          border-radius:5px;
          text-decoration:none;
          margin-left:5px;">
    Manage Assignments
</a>

</div>

</body>

</html>