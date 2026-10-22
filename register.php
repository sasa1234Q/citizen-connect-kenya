<?php

require_once "config/database.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullname = trim($_POST["fullname"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);

    if ($fullname === "" || $email === "") {

        $message = "Please fill in all required fields.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } else {

        /* Check whether email already exists */

        $check = $conn->prepare(
            "SELECT User_id FROM `user` WHERE User_email = ?"
        );

        $check->bind_param("s", $email);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "An account with this email already exists.";
            $message_type = "error";

        } else {

            /* Create citizen account */

            $role = "Citizen";
            $status = "Active";

            $stmt = $conn->prepare(
                "INSERT INTO `user`
                (
                    User_full_name,
                    User_email,
                    User_password,
                    User_phone,
                    User_role,
                    User_status
                )
                VALUES (?, ?, NULL, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sssss",
                $fullname,
                $email,
                $phone,
                $role,
                $status
            );

            if ($stmt->execute()) {

                header("Location: login.php?registered=1");
                exit;

            } else {

                $message = "Account creation failed. Please try again.";
                $message_type = "error";
            }

            $stmt->close();
        }

        $check->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Create Account - CitizenConnect Kenya</title>

    <link rel="stylesheet"
          href="assets/css/style.css">

</head>

<body class="auth-body">

    <div class="auth-container">

        <div class="auth-brand">

            <div class="auth-logo">
                <span>KE</span> CitizenConnect Kenya
            </div>

            <p>
                Citizen Registration Portal
            </p>

        </div>


        <div class="auth-card">

            <h1>
                Create Your Account
            </h1>

            <p class="auth-subtitle">
                Register to access CitizenConnect Kenya services.
            </p>


            <?php if ($message !== ""): ?>

                <div class="auth-message <?php echo $message_type; ?>">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>


            <form method="POST"
                  action="register.php">


                <div class="form-group">

                    <label>
                        Full Name
                    </label>

                    <input
                        type="text"
                        name="fullname"
                        placeholder="Enter your full name"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Email Address
                    </label>

                    <input
                        type="email"
                        name="email"
                        placeholder="Enter your email address"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Phone Number
                    </label>

                    <input
                        type="text"
                        name="phone"
                        placeholder="Enter your phone number"
                    >

                </div>


                <button
                    type="submit"
                    class="auth-submit">

                    Create Account

                </button>


            </form>


            <div class="auth-footer">

                Already have an account?

                <a href="login.php">
                    Secure Login
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