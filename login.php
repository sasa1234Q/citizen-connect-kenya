<?php

session_start();

require_once "config/database.php";

$message = "";
$message_type = "";

if (isset($_GET["registered"])) {

    $message = "Account created successfully. You can now log in using your email.";
    $message_type = "success";
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);

    if ($email === "") {

        $message = "Please enter your email address.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } else {

        $stmt = $conn->prepare(
            "SELECT
                User_id,
                User_full_name,
                User_email,
                User_role,
                User_status
             FROM `user`
             WHERE User_email = ?
             LIMIT 1"
        );

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if ($user["User_status"] !== "Active") {

                $message = "Your account is currently inactive.";
                $message_type = "error";

            } elseif ($user["User_role"] !== "Citizen") {

                $message = "Please use the Admin Panel for administrator access.";
                $message_type = "error";

            } else {

                $_SESSION["User_id"] = $user["User_id"];
                $_SESSION["User_full_name"] = $user["User_full_name"];
                $_SESSION["User_email"] = $user["User_email"];
                $_SESSION["User_role"] = $user["User_role"];

                header("Location: citizen/dashboard.php");
                exit;
            }

        } else {

            $message = "No citizen account was found with that email.";
            $message_type = "error";
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Secure Login - CitizenConnect Kenya</title>

    <link rel="stylesheet"
          href="assets/css/style.css">

</head>

<body class="auth-body">


<div class="auth-container">

    <div class="auth-brand">

        <div class="auth-logo">

            <span>KE</span>
            CitizenConnect Kenya

        </div>

        <p>
            Citizen Secure Login
        </p>

    </div>


    <div class="auth-card">

        <h1>
            Secure Login
        </h1>

        <p class="auth-subtitle">

            Enter your registered email to access
            your citizen dashboard.

        </p>


        <?php if ($message !== ""): ?>

            <div class="auth-message <?php echo $message_type; ?>">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>


        <form method="POST"
              action="login.php">


            <div class="form-group">

                <label>
                    Email Address
                </label>

                <input
                    type="email"
                    name="email"
                    placeholder="Enter your registered email"
                    required
                >

            </div>


            <button
                type="submit"
                class="auth-submit">

                Login to Citizen Dashboard

            </button>


        </form>


        <div class="auth-footer">

            Don't have an account?

            <a href="register.php">
                Create Account
            </a>

        </div>


        <a href="index.php"
           class="back-home">

            ← Back to Home

        </a>

    </div>

</div>


</body>

</html>