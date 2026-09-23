<?php
session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
    exit();
}

$user_id = $_GET["id"] ?? 0;

if ($user_id <= 0) {
    die("Invalid user ID.");
}

$sql = "SELECT *
        FROM users
        WHERE user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("User not found.");
}

$user = $result->fetch_assoc();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $role = $_POST["role"];

    $sql = "UPDATE users
            SET name = ?,
                email = ?,
                role = ?
            WHERE user_id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sssi",
        $name,
        $email,
        $role,
        $user_id
    );

    if ($stmt->execute()) {

        $message = "User updated successfully.";

        $user["name"] = $name;
        $user["email"] = $email;
        $user["role"] = $role;

    } else {

        $message = "Error updating user.";

    }
}
?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit User - EduBridge LMS</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<div class="header">
    <h1>EduBridge Academy LMS</h1>
</div>

<div class="container">

    <div class="card">

        <h2>Edit User</h2>

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
                value="<?php echo htmlspecialchars($user["name"]); ?>"
                required
            >

            <br><br>

            <label>Email</label>

            <input
                type="email"
                name="email"
                value="<?php echo htmlspecialchars($user["email"]); ?>"
                required
            >

            <br><br>

            <label>Role</label>

            <select name="role" required>

                <option
                    value="student"
                    <?php echo $user["role"] == "student" ? "selected" : ""; ?>>
                    Student
                </option>

                <option
                    value="teacher"
                    <?php echo $user["role"] == "teacher" ? "selected" : ""; ?>>
                    Teacher
                </option>

                <option
                    value="admin"
                    <?php echo $user["role"] == "admin" ? "selected" : ""; ?>>
                    Admin
                </option>

            </select>

            <br><br>

            <button type="submit">
                Save Changes
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