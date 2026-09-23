<?php
session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
    exit();
}

$message = "";

/* Get Teachers */

$sql = "SELECT user_id, name
        FROM users
        WHERE role = 'teacher'
        ORDER BY name";

$teachers = $conn->query($sql);

/* Add Course */

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

        $sql = "INSERT INTO courses
                (
                    course_name,
                    instructor_id
                )
                VALUES (?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "si",
            $course_name,
            $instructor_id
        );

        if ($stmt->execute()) {

            $message =
                "Course added successfully.";

        } else {

            $message =
                "Error adding course.";

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

    <title>Add Course - EduBridge LMS</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<div class="header">
    <h1>EduBridge Academy LMS</h1>
</div>

<div class="container">

    <div class="card">

        <h2>Add Course</h2>

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
                required
            >

            <br><br>

            <label>
                Instructor
            </label>

            <select
                name="instructor_id"
                required>

                <option value="">
                    Select Teacher
                </option>

                <?php while ($teacher = $teachers->fetch_assoc()): ?>

                    <option value="<?php echo $teacher["user_id"]; ?>">

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
                Add Course
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