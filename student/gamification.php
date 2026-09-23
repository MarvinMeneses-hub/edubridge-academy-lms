<?php
session_start();
include '../db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "student") {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$name = $_SESSION["name"];

/* Count submissions */
$sql = "SELECT COUNT(*) AS total
        FROM submissions
        WHERE user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$total_submissions = $result->fetch_assoc()["total"];

/* Count graded submissions */
$sql = "SELECT COUNT(*) AS total
        FROM submissions
        WHERE user_id = ?
        AND grade IS NOT NULL";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$total_graded = $result->fetch_assoc()["total"];

/* Calculate points */
$submission_points = $total_submissions * 10;
$grading_points = $total_graded * 5;
$total_points = $submission_points + $grading_points;
?>

<!DOCTYPE html>
<html>
<head>

<title>Achievements - EduBridge LMS</title>

<link rel="stylesheet" href="../css/style.css">

</head>

<body>

<div class="header">

    <h1>EduBridge Academy LMS</h1>

    <p>
        Welcome,
        <?php echo htmlspecialchars($name); ?>!
    </p>

    <div class="button-group">

        <a href="dashboard.php" class="btn">
            Dashboard
        </a>

        <a href="../logout.php" class="btn btn-back">
            Logout
        </a>

    </div>

</div>


<div class="container">

    <div class="card">

        <h2>My Achievements</h2>

        <p>
            Complete assignments and get your work graded
            to earn points and unlock achievements.
        </p>

    </div>


    <div class="achievement-box">

        <div class="achievement-stat">

            <p>Total Points</p>

            <h2>
                <?php echo $total_points; ?>
            </h2>

        </div>


        <div class="achievement-stat">

            <p>Assignments Submitted</p>

            <h2>
                <?php echo $total_submissions; ?>
            </h2>

        </div>


        <div class="achievement-stat">

            <p>Assignments Graded</p>

            <h2>
                <?php echo $total_graded; ?>
            </h2>

        </div>

    </div>


    <div class="card">

        <h2>Badges</h2>

        <div class="badges">


            <!-- Getting Started -->

            <div class="badge <?php echo ($total_submissions >= 1) ? 'unlocked' : ''; ?>">

                <div class="badge-icon">
                    🏅
                </div>

                <h3>Getting Started</h3>

                <p>
                    Submit your first assignment.
                </p>

                <?php if ($total_submissions >= 1): ?>

                    <strong>Unlocked</strong>

                <?php else: ?>

                    <span>Locked</span>

                <?php endif; ?>

            </div>


            <!-- Active Learner -->

            <div class="badge <?php echo ($total_submissions >= 3) ? 'unlocked' : ''; ?>">

                <div class="badge-icon">
                    📚
                </div>

                <h3>Active Learner</h3>

                <p>
                    Submit 3 assignments.
                </p>

                <?php if ($total_submissions >= 3): ?>

                    <strong>Unlocked</strong>

                <?php else: ?>

                    <span>Locked</span>

                <?php endif; ?>

            </div>


            <!-- Dedicated Student -->

            <div class="badge <?php echo ($total_submissions >= 5) ? 'unlocked' : ''; ?>">

                <div class="badge-icon">
                    ⭐
                </div>

                <h3>Dedicated Student</h3>

                <p>
                    Submit 5 assignments.
                </p>

                <?php if ($total_submissions >= 5): ?>

                    <strong>Unlocked</strong>

                <?php else: ?>

                    <span>Locked</span>

                <?php endif; ?>

            </div>


            <!-- Achievement Unlocked -->

            <div class="badge <?php echo ($total_graded >= 3) ? 'unlocked' : ''; ?>">

                <div class="badge-icon">
                    🎓
                </div>

                <h3>Achievement Unlocked</h3>

                <p>
                    Get 3 assignments graded.
                </p>

                <?php if ($total_graded >= 3): ?>

                    <strong>Unlocked</strong>

                <?php else: ?>

                    <span>Locked</span>

                <?php endif; ?>

            </div>


            <!-- EduBridge Star -->

            <div class="badge <?php echo ($total_graded >= 5) ? 'unlocked' : ''; ?>">

                <div class="badge-icon">
                    🌟
                </div>

                <h3>EduBridge Star</h3>

                <p>
                    Get 5 assignments graded.
                </p>

                <?php if ($total_graded >= 5): ?>

                    <strong>Unlocked</strong>

                <?php else: ?>

                    <span>Locked</span>

                <?php endif; ?>

            </div>

        </div>

    </div>


    <div class="card">

        <h2>How to Earn Points</h2>

        <div class="course">

            <p>
                <strong>+10 points</strong>
                — Submit an assignment
            </p>

            <p>
                <strong>+5 points</strong>
                — Get an assignment graded
            </p>

        </div>

    </div>


</div>

</body>
</html>