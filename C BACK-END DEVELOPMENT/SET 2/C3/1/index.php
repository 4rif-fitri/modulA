<?php

/*
|--------------------------------------------------------------------------
| DATABASE CONNECTION
|--------------------------------------------------------------------------
*/

$host = "localhost";
$user = "root";
$pass = "";
$db = "contact_system";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database connection failed");
}

/*
|--------------------------------------------------------------------------
| DELETE SUBMISSION
|--------------------------------------------------------------------------
*/

if (isset($_GET["delete"])) {

    $id = (int)$_GET["delete"];

    $stmt = mysqli_prepare($conn, "
        DELETE FROM contact_submissions
        WHERE id = ?
    ");

    mysqli_stmt_bind_param($stmt, "i", $id);

    mysqli_stmt_execute($stmt);

    header("Location: " . $_SERVER["PHP_SELF"]);
    exit();
}

/*
|--------------------------------------------------------------------------
| FORM SUBMIT
|--------------------------------------------------------------------------
*/

$success = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = htmlspecialchars(trim($_POST["name"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $subject = htmlspecialchars(trim($_POST["subject"]));
    $message = htmlspecialchars(trim($_POST["message"]));

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if ($name == "") {
        $errors[] = "Name is required";
    }

    if ($email == "") {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if ($subject == "") {
        $errors[] = "Subject is required";
    }

    if ($message == "") {
        $errors[] = "Message is required";
    } elseif (strlen($message) < 10) {
        $errors[] = "Message must be at least 10 characters";
    }

    /*
    |--------------------------------------------------------------------------
    | SEND EMAIL
    |--------------------------------------------------------------------------
    */

    if (empty($errors)) {

        $adminEmail = "admin@example.com";

        $emailBody = "
        Name: $name

        Email: $email

        Subject: $subject

        Message:
        $message
        ";

        $headers = "From: $email";

        // $emailSent = mail(
        //     $adminEmail,
        //     $subject,
        //     $emailBody,
        //     $headers
        // );

        $emailSent = true;

        /*
        |--------------------------------------------------------------------------
        | STORE DATABASE
        |--------------------------------------------------------------------------
        */

        $stmt = mysqli_prepare($conn, "
            INSERT INTO contact_submissions
            (
                name,
                email,
                subject,
                message,
                email_sent
            )
            VALUES (?, ?, ?, ?, ?)
        ");

        $sent = $emailSent ? 1 : 0;

        mysqli_stmt_bind_param(
            $stmt,
            "ssssi",
            $name,
            $email,
            $subject,
            $message,
            $sent
        );

        mysqli_stmt_execute($stmt);

        $success = "Contact form submitted successfully";
    }
}

/*
|--------------------------------------------------------------------------
| GET SUBMISSIONS
|--------------------------------------------------------------------------
*/

$result = mysqli_query($conn, "
    SELECT *
    FROM contact_submissions
    ORDER BY submitted_at DESC
");

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Contact Form System</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial;
            background: #f2f2f2;
            padding: 30px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        h1,
        h2 {
            margin-bottom: 20px;
        }

        input,
        textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        textarea {
            height: 180px;
            resize: none;
        }

        button {
            padding: 12px 20px;
            border: none;
            background: #222;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }

        button:hover {
            background: #444;
        }

        .success {
            padding: 15px;
            background: #d4edda;
            color: green;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .error {
            padding: 15px;
            background: #f8d7da;
            color: red;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        table th,
        table td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: left;
        }

        table th {
            background: #eee;
        }

        .delete {
            color: red;
            text-decoration: none;
        }

        .status-success {
            color: green;
            font-weight: bold;
        }

        .status-fail {
            color: red;
            font-weight: bold;
        }

        @media(max-width:768px) {

            table {
                font-size: 14px;
            }

        }
    </style>

</head>

<body>

    <div class="container">

        <h1>Contact Form</h1>

        <!-- SUCCESS -->

        <?php if ($success): ?>

            <div class="success">
                <?php echo $success; ?>
            </div>

        <?php endif; ?>

        <!-- ERRORS -->

        <?php foreach ($errors as $error): ?>

            <div class="error">
                <?php echo $error; ?>
            </div>

        <?php endforeach; ?>

        <!-- CONTACT FORM -->

        <form method="POST">

            <input
                type="text"
                name="name"
                placeholder="Your Name"
                required>

            <input
                type="email"
                name="email"
                placeholder="Your Email"
                required>

            <input
                type="text"
                name="subject"
                placeholder="Subject"
                required>

            <textarea
                name="message"
                placeholder="Enter message..."
                required></textarea>

            <button type="submit">
                Send Message
            </button>

        </form>

        <hr style="margin:40px 0;">

        <!-- ADMIN PANEL -->

        <h2>Admin Panel - Contact Submissions</h2>

        <table>

            <tr>

                <th>ID</th>

                <th>Name</th>

                <th>Email</th>

                <th>Subject</th>

                <th>Date</th>

                <th>Email Status</th>

                <th>Action</th>

            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)): ?>

                <tr>

                    <td>
                        <?php echo $row["id"]; ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["name"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["email"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["subject"]); ?>
                    </td>

                    <td>
                        <?php echo $row["submitted_at"]; ?>
                    </td>

                    <td>

                        <?php if ($row["email_sent"]): ?>

                            <span class="status-success">
                                Sent
                            </span>

                        <?php else: ?>

                            <span class="status-fail">
                                Failed
                            </span>

                        <?php endif; ?>

                    </td>

                    <td>

                        <a
                            class="delete"
                            href="?delete=<?php echo $row["id"]; ?>"
                            onclick="return confirm('Delete submission?')">

                            Delete

                        </a>

                    </td>

                </tr>

            <?php endwhile; ?>

        </table>

    </div>

</body>

</html>