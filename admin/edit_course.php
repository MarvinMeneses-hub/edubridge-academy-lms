<?php
session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
    exit();
}

$course_id = $_GET["id"] ?? 0;

if ($course_id <= 0) {
    die("Invalid course ID.");
}

/* Get Course */

$sql = "SELECT *
        FROM courses
        WHERE course_id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $course_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Course not found.");
}

$course = $result->fetch_assoc();

$message = "";

/* Get Teachers */

$sql = "SELECT user_id, name
        FROM users
        WHERE role = 'teacher'
        ORDER BY name";

$teachers = $conn->query($sql);

/* Update */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $course_name =
        trim($_POST["course_name"]);

    $instructor_id =
        $_POST["instructor_id"];

    if ($course_name == "" ||
        $instructor_id == "") {

        $message =
            "Please complete all fields.";

    } else {

        $sql = "UPDATE courses

                SET course_name = ?,
                    instructor_id = ?

                WHERE course_id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "sii",
            $course_name,
            $instructor_id,
            $course_id
        );

        if ($stmt->execute()) {

            $message =
                "Course updated successfully.";

            $course["course_name"] =
                $course_name;

            $course["instructor_id"] =
                $instructor_id;

        } else {

            $message =
                "Error updating course.";

        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Course - EduBridge LMS</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<div class="header">
    <h1>EduBridge Academy LMS</h1>
</div>

<div class="container">

    <div class="card">

        <h2>Edit Course</h2>

        <?php if ($message != ""): ?>

            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <label>
                Course Name
            </label>

            <input
                type="text"
                name="course_name"
                value="<?php echo htmlspecialchars($course["course_name"]); ?>"
                required
            >

            <br><br>

            <label>
                Instructor
            </label>

            <select
                name="instructor_id"
                required>

                <?php while ($teacher = $teachers->fetch_assoc()): ?>

                    <option
                        value="<?php echo $teacher["user_id"]; ?>"
                        <?php
                        echo $course["instructor_id"] ==
                             $teacher["user_id"]
                             ? "selected"
                             : "";
                        ?>>

                        <?php
                        echo htmlspecialchars(
                            $teacher["name"]
                        );
                        ?>

                    </option>

                <?php endwhile; ?>

            </select>

            <br><br>

            <button type="submit">
                Save Changes
            </button>

        </form>

        <div class="button-group">

            <a href="courses.php"
               class="btn btn-back">
                ← Back to Courses
            </a>

        </div>

    </div>

</div>

</body>
</html>