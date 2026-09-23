<?php
session_start();
include 'db.php';

$message = "";

/* Login process */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($email == "" || $password == "") {

        $message = "Please enter your email and password.";

    } else {

        $sql = "SELECT *
                FROM users
                WHERE email = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $user = $result->fetch_assoc();

            /*
             * Current prototype uses plain-text passwords.
             * This matches the users currently stored
             * in your database.
             */
            if ($password === $user["password"]) {

                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["role"] = $user["role"];
                $_SESSION["email"] = $user["email"];

                /* Redirect based on role */

                if ($user["role"] == "student") {

                    header("Location: student/dashboard.php");
                    exit();

                } elseif ($user["role"] == "teacher") {

                    header("Location: teacher/dashboard.php");
                    exit();

                } elseif ($user["role"] == "admin") {

                    header("Location: admin/dashboard.php");
                    exit();

                }

            } else {

                $message = "Incorrect email or password.";

            }

        } else {

            $message = "Incorrect email or password.";

        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login - EduBridge Academy LMS</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>

<body class="login-page">


    <!-- =================================================
         DECORATIVE BACKGROUND SHAPES
         ================================================= -->

    <div class="login-shape login-shape-one"></div>

    <div class="login-shape login-shape-two"></div>

    <div class="login-shape login-shape-three"></div>


    <!-- =================================================
         LOGIN CONTENT
         ================================================= -->

    <main class="login-container">


        <!-- BRAND -->

        <div class="login-brand">

            <div class="login-logo">
                E
            </div>

            <h1>
                EduBridge
            </h1>

            <span>
                Academy LMS
            </span>

        </div>


        <!-- WELCOME MESSAGE -->

        <div class="login-welcome">

            <h2>
                Welcome back!
            </h2>

            <p>
                Sign in to continue to your learning dashboard.
            </p>

        </div>


        <!-- LOGIN CARD -->

        <div class="login-card">


            <?php if ($message != ""): ?>

                <div class="login-message">

                    <?php
                    echo htmlspecialchars($message);
                    ?>

                </div>

            <?php endif; ?>


            <form
                method="POST"
                action=""
            >


                <!-- EMAIL -->

                <div class="login-form-group">

                    <label for="email">
                        Your email
                    </label>

                    <div class="login-input-wrapper">

                        <span class="login-input-icon">
                            ✉
                        </span>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="e.g. marvin@student.com"
                            autocomplete="email"
                            required
                        >

                    </div>

                </div>


                <!-- PASSWORD -->

                <div class="login-form-group">

                    <label for="password">
                        Your password
                    </label>

                    <div class="login-input-wrapper">

                        <span class="login-input-icon">
                            🔒
                        </span>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >

                    </div>

                </div>


                <!-- LOGIN BUTTON -->

                <button
                    type="submit"
                    class="login-button"
                >
                    Sign In
                </button>


            </form>


            <!-- DEMO INFORMATION -->

            <div class="login-help">

                <p>
                    Use your registered EduBridge account
                    to access your dashboard.
                </p>

            </div>


        </div>


        <!-- FOOTER -->

        <div class="login-footer">

            <p>
                EduBridge Academy LMS
            </p>

            <span>
                Learning made simpler.
            </span>

        </div>


    </main>

</body>

</html>