<?php
session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "student") {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$assignment_id =
    $_GET["assignment_id"] ?? null;

if (!$assignment_id) {
    die("No assignment selected.");
}

/* Get Assignment */

$sql = "SELECT assignments.*,
               courses.course_name
        FROM assignments
        INNER JOIN courses
        ON assignments.course_id = courses.course_id
        WHERE assignments.assignment_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $assignment_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Assignment not found.");
}

$assignment = $result->fetch_assoc();

/* Check Enrollment */

$sql = "SELECT *
        FROM enrollments
        WHERE user_id = ?
        AND course_id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ii",
    $user_id,
    $assignment["course_id"]
);

$stmt->execute();

$enrollment = $stmt->get_result();

if ($enrollment->num_rows == 0) {
    die("You are not enrolled in this course.");
}

$message = "";

/* Submit */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $submission_text =
        trim($_POST["submission_text"]);

    if ($submission_text != "") {

        /* Check existing */

        $sql = "SELECT *
                FROM submissions
                WHERE user_id = ?
                AND assignment_id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "ii",
            $user_id,
            $assignment_id
        );

        $stmt->execute();

        $existing = $stmt->get_result();

        if ($existing->num_rows > 0) {

            $message =
                "You have already submitted this assignment.";

        } else {

            $sql = "INSERT INTO submissions
                    (
                        user_id,
                        assignment_id,
                        submission_text
                    )
                    VALUES (?, ?, ?)";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "iis",
                $user_id,
                $assignment_id,
                $submission_text
            );

            if ($stmt->execute()) {

                $message =
                    "Assignment submitted successfully!";

            } else {

                $message =
                    "Error submitting assignment.";

            }
        }

    } else {

        $message =
            "Please enter your submission.";

    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Submit Assignment - EduBridge LMS</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<div class="header">

    <h1>EduBridge Academy LMS</h1>

</div>

<div class="container">

    <div class="card">

        <h2>Submit Assignment</h2>

        <h3>
            <?php echo htmlspecialchars($assignment["title"]); ?>
        </h3>

        <p>
            <strong>Course:</strong>
            <?php echo htmlspecialchars($assignment["course_name"]); ?>
        </p>

        <p>
            <?php echo nl2br(
                htmlspecialchars($assignment["description"])
            ); ?>
        </p>

        <p>
            <strong>Due Date:</strong>
            <?php echo htmlspecialchars($assignment["due_date"]); ?>
        </p>

        <?php if ($message != ""): ?>

            <div class="message">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>

        <form method="POST">

            <label>
                Your Submission
            </label>

            <textarea
                name="submission_text"
                placeholder="Enter your answer or submission here..."
                required></textarea>

            <button type="submit">
                Submit Assignment
            </button>

        </form>

        <div class="button-group">

            <a href="dashboard.php"
               class="btn btn-back">
                ← Back to Dashboard
            </a>

        </div>

    </div>

</div>

</body>
</html>