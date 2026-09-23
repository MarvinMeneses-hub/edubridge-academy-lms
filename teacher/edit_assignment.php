<?php
session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "teacher") {
    header("Location: ../login.php");
    exit();
}

$teacher_id = $_SESSION["user_id"];
$assignment_id = $_GET["id"] ?? 0;

if ($assignment_id <= 0) {
    die("Invalid assignment ID.");
}


/* Get assignment */

$sql = "SELECT
            assignments.*,
            courses.course_name
        FROM assignments
        INNER JOIN courses
        ON assignments.course_id = courses.course_id
        WHERE assignments.assignment_id = ?
        AND courses.instructor_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $assignment_id, $teacher_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Assignment not found or you do not have permission to edit it.");
}

$assignment = $result->fetch_assoc();

$message = "";


/* Update assignment */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $due_date = $_POST["due_date"] ?? "";

    if ($title == "" || $description == "" || $due_date == "") {

        $message = "Please complete all fields.";

    } else {

        $sql = "UPDATE assignments
                SET title = ?,
                    description = ?,
                    due_date = ?
                WHERE assignment_id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "sssi",
            $title,
            $description,
            $due_date,
            $assignment_id
        );

        if ($stmt->execute()) {

            header("Location: assignments.php");
            exit();

        } else {

            $message = "Error updating assignment.";

        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>

<title>Edit Assignment - EduBridge LMS</title>

<link rel="stylesheet" href="../css/style.css">

</head>

<body>

<div class="header">

    <h1>EduBridge Academy LMS</h1>

    <p>Edit Assignment</p>

</div>


<div class="container">

    <div class="card">

        <h2>Edit Assignment</h2>

        <?php if ($message != ""): ?>

            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>


        <form method="POST">

            <div class="form-group">

                <label>
                    Assignment Title
                </label>

                <input
                    type="text"
                    name="title"
                    value="<?php echo htmlspecialchars($assignment["title"]); ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Description
                </label>

                <textarea
                    name="description"
                    required
                ><?php echo htmlspecialchars($assignment["description"]); ?></textarea>

            </div>


            <div class="form-group">

                <label>
                    Due Date
                </label>

                <input
                    type="date"
                    name="due_date"
                    value="<?php echo htmlspecialchars($assignment["due_date"]); ?>"
                    required
                >

            </div>


            <div class="button-group">

                <button type="submit" class="btn">
                    Save Changes
                </button>

                <a href="assignments.php" class="btn btn-back">
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>

</body>
</html>