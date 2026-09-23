<?php
session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $role = $_POST["role"];

    if ($name == "" ||
        $email == "" ||
        $password == "") {

        $message = "Please complete all fields.";

    } else {

        $sql = "SELECT user_id
                FROM users
                WHERE email = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $existing = $stmt->get_result();

        if ($existing->num_rows > 0) {

            $message = "Email already exists.";

        } else {

            $sql = "INSERT INTO users
                    (name, email, password, role)
                    VALUES (?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "ssss",
                $name,
                $email,
                $password,
                $role
            );

            if ($stmt->execute()) {

                $message =
                    "User added successfully.";

            } else {

                $message =
                    "Error adding user.";

            }
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

    <title>Add User - EduBridge LMS</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<div class="header">
    <h1>EduBridge Academy LMS</h1>
</div>

<div class="container">

    <div class="card">

        <h2>Add User</h2>

        <?php if ($message != ""): ?>

            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <label>Name</label>

            <input
                type="text"
                name="name"
                required
            >

            <br><br>

            <label>Email</label>

            <input
                type="email"
                name="email"
                required
            >

            <br><br>

            <label>Password</label>

            <input
                type="password"
                name="password"
                required
            >

            <br><br>

            <label>Role</label>

            <select name="role" required>

                <option value="student">
                    Student
                </option>

                <option value="teacher">
                    Teacher
                </option>

                <option value="admin">
                    Admin
                </option>

            </select>

            <br><br>

            <button type="submit">
                Add User
            </button>

        </form>

        <div class="button-group">

            <a href="users.php"
               class="btn btn-back">
                ← Back to Users
            </a>

        </div>

    </div>

</div>

</body>
</html>