<?php
session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "teacher") {
    header("Location: ../login.php");
    exit();
}

$teacher_id = $_SESSION["user_id"];
$submission_id = $_GET["id"] ?? 0;

$error = "";

// Get submission information
$sql = "SELECT
            submissions.submission_id,
            submissions.submission_text,
            submissions.submitted_at,
            submissions.grade,
            users.name AS student_name,
            assignments.title AS assignment_title,
            courses.course_name
        FROM submissions
        INNER JOIN users
            ON submissions.user_id = users.user_id
        INNER JOIN assignments
            ON submissions.assignment_id = assignments.assignment_id
        INNER JOIN courses
            ON assignments.course_id = courses.course_id
        WHERE submissions.submission_id = ?
        AND courses.instructor_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $submission_id, $teacher_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows != 1) {
    die("Submission not found or you do not have permission to grade it.");
}

$submission = $result->fetch_assoc();


// Save grade
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $grade = $_POST["grade"];

    if ($grade === "" || !is_numeric($grade)) {

        $error = "Please enter a valid grade.";

    } elseif ($grade < 0 || $grade > 100) {

        $error = "Grade must be between 0 and 100.";

    } else {

        $sql = "UPDATE submissions
                SET grade = ?
                WHERE submission_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("di", $grade, $submission_id);

        if ($stmt->execute()) {
            header("Location: submissions.php");
            exit();
        } else {
            $error = "Failed to save the grade.";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Grade Submission - EduBridge LMS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<div class="header">

    <h1>EduBridge Academy LMS</h1>

    <p>Grade Student Submission</p>

</div>


<div class="container">

    <div class="card">

        <h2>Submission Details</h2>

        <div class="info">
            <strong>Student:</strong>
            <?php echo htmlspecialchars($submission["student_name"]); ?>
        </div>

        <div class="info">
            <strong>Course:</strong>
            <?php echo htmlspecialchars($submission["course_name"]); ?>
        </div>

        <div class="info">
            <strong>Assignment:</strong>
            <?php echo htmlspecialchars($submission["assignment_title"]); ?>
        </div>

        <div class="info">
            <strong>Submitted:</strong>
            <?php echo htmlspecialchars($submission["submitted_at"]); ?>
        </div>

        <h3>Student Submission</h3>

        <div class="submission">
            <?php echo htmlspecialchars($submission["submission_text"]); ?>
        </div>

    </div>


    <div class="card">

        <h2>Enter Grade</h2>

        <?php if ($error != ""): ?>

            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>


        <form method="POST">

            <label for="grade">Grade (0 - 100)</label>

            <input
                type="number"
                id="grade"
                name="grade"
                min="0"
                max="100"
                step="0.01"
                value="<?php echo htmlspecialchars($submission["grade"] ?? ""); ?>"
                required
            >

            <button type="submit">
                Save Grade
            </button>

        </form>

        <a class="back" href="submissions.php">
            ← Back to Submissions
        </a>

    </div>

</div>

</body>

</html>