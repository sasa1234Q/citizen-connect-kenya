<?php

require_once "admin_header.php";


/*
|--------------------------------------------------------------------------
| USER ID
|--------------------------------------------------------------------------
*/

if (
    !isset($_GET["id"]) ||
    !is_numeric($_GET["id"])
) {

    header("Location: users.php");
    exit;

}


$user_id = (int) $_GET["id"];


/*
|--------------------------------------------------------------------------
| GET USER
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("

    SELECT

        User_id,
        User_full_name,
        User_email,
        User_phone,
        User_role,
        User_status,
        User_created_at

    FROM `user`

    WHERE User_id = ?

    LIMIT 1

");


$stmt->bind_param(
    "i",
    $user_id
);


$stmt->execute();


$result = $stmt->get_result();


if ($result->num_rows !== 1) {

    header("Location: users.php");
    exit;

}


$user = $result->fetch_assoc();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        User Details - Admin
    </title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

</head>


<body class="dashboard-body">


<?php require_once "admin_sidebar.php"; ?>


<main class="dashboard-main">


    <div class="dashboard-header">

        <div>

            <h1>
                User #<?php
                echo $user["User_id"];
                ?>
            </h1>

            <p class="page-description">

                View registered user information.

            </p>

        </div>


        <a
            href="users.php"
            class="back-dashboard-btn"
        >

            ← Back to Users

        </a>

    </div>


    <!-- =====================================================
         USER INFORMATION
         ===================================================== -->

    <section class="complaint-detail-card">


        <div class="detail-card-header">

            <h2>
                User Information
            </h2>

        </div>


        <div class="detail-row">

            <span>
                Full Name
            </span>

            <strong>

                <?php
                echo htmlspecialchars(
                    $user["User_full_name"]
                );
                ?>

            </strong>

        </div>


        <div class="detail-row">

            <span>
                Email Address
            </span>

            <strong>

                <?php
                echo htmlspecialchars(
                    $user["User_email"]
                );
                ?>

            </strong>

        </div>


        <div class="detail-row">

            <span>
                Phone Number
            </span>

            <strong>

                <?php

                echo !empty(
                    $user["User_phone"]
                )
                ?
                htmlspecialchars(
                    $user["User_phone"]
                )
                :
                "Not provided";

                ?>

            </strong>

        </div>


        <div class="detail-row">

            <span>
                Account Role
            </span>

            <strong>

                <?php
                echo htmlspecialchars(
                    $user["User_role"]
                );
                ?>

            </strong>

        </div>


        <div class="detail-row">

            <span>
                Account Status
            </span>

            <strong>

                <?php
                echo htmlspecialchars(
                    $user["User_status"]
                );
                ?>

            </strong>

        </div>


        <div class="detail-row">

            <span>
                Registration Date
            </span>

            <strong>

                <?php

                echo date(
                    "F d, Y h:i A",
                    strtotime(
                        $user["User_created_at"]
                    )
                );

                ?>

            </strong>

        </div>


    </section>


</main>


</body>

</html>