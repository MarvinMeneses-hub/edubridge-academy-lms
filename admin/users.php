<?php
session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
    exit();
}

$sql = "SELECT *
        FROM users
        ORDER BY role, name";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manage Users - EduBridge LMS</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<div class="header">

    <h1>EduBridge Academy LMS</h1>

</div>

<div class="container">

    <div class="card">

        <h2>Manage Users</h2>

        <div class="button-group">

            <a href="add_user.php"
               class="btn">
                + Add User
            </a>

        </div>

        <br>

        <table>

            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>

            <?php while ($user = $result->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?php echo $user["user_id"]; ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($user["name"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($user["email"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($user["role"]); ?>
                    </td>

                    <td>

                        <a
                            href="edit_user.php?id=<?php echo $user["user_id"]; ?>"
                            class="btn">
                            Edit
                        </a>

                        <?php if ($user["user_id"] != $_SESSION["user_id"]): ?>

                            <a
                                href="delete_user.php?id=<?php echo $user["user_id"]; ?>"
                                class="btn btn-danger"
                                onclick="return confirm('Delete this user?');">
                                Delete
                            </a>

                        <?php endif; ?>

                    </td>

                </tr>

            <?php endwhile; ?>

        </table>

        <div class="button-group">

            <a href="dashboard.php"
               class="btn btn-back">
                ← Dashboard
            </a>

        </div>

    </div>

</div>

</body>
</html>