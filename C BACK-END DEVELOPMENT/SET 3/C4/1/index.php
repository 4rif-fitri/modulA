<?php

/*
|--------------------------------------------------------------------------
| DATABASE CONNECTION
|--------------------------------------------------------------------------
*/

$host = "localhost";
$user = "root";
$pass = "";
$db = "appointment_system";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database connection failed");
}

/*
|--------------------------------------------------------------------------
| CANCEL BOOKING (ADMIN)
|--------------------------------------------------------------------------
*/

if (isset($_GET["cancel"])) {

    $id = (int)$_GET["cancel"];

    mysqli_query($conn, "
        UPDATE bookings
        SET status = 'cancelled'
        WHERE id = $id
    ");

    header("Location: " . $_SERVER["PHP_SELF"]);
    exit();
}

/*
|--------------------------------------------------------------------------
| CREATE BOOKING
|--------------------------------------------------------------------------
*/

$errors = [];

if (isset($_POST["book"])) {

    $service_id = (int)$_POST["service_id"];
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $date = $_POST["date"];
    $time = $_POST["time"];

    $today = date("Y-m-d");

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if ($name == "" || $email == "" || $phone == "" || $date == "" || $time == "") {
        $errors[] = "All fields are required";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if ($date < $today) {
        $errors[] = "Date cannot be in the past";
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK AVAILABILITY
    |--------------------------------------------------------------------------
    */

    $check = mysqli_query($conn, "
        SELECT * FROM bookings
        WHERE booking_date = '$date'
        AND booking_time = '$time'
        AND status = 'active'
    ");

    if (mysqli_num_rows($check) > 0) {
        $errors[] = "Time slot already booked";
    }

    /*
    |--------------------------------------------------------------------------
    | INSERT BOOKING
    |--------------------------------------------------------------------------
    */

    if (empty($errors)) {

        $stmt = mysqli_prepare($conn, "
            INSERT INTO bookings
            (service_id, customer_name, customer_email, customer_phone, booking_date, booking_time, status)
            VALUES (?, ?, ?, ?, ?, ?, 'active')
        ");

        mysqli_stmt_bind_param(
            $stmt,
            "isssss",
            $service_id,
            $name,
            $email,
            $phone,
            $date,
            $time
        );

        mysqli_stmt_execute($stmt);

        header("Location: " . $_SERVER["PHP_SELF"]);
        exit();
    }
}

/*
|--------------------------------------------------------------------------
| FILTER
|--------------------------------------------------------------------------
*/

$where = "WHERE 1=1";

if (!empty($_GET["filter_date"])) {
    $fd = $_GET["filter_date"];
    $where .= " AND booking_date = '$fd'";
}

if (!empty($_GET["filter_service"])) {
    $fs = (int)$_GET["filter_service"];
    $where .= " AND service_id = $fs";
}

/*
|--------------------------------------------------------------------------
| SERVICES
|--------------------------------------------------------------------------
*/

$services = mysqli_query($conn, "SELECT * FROM services");

/*
|--------------------------------------------------------------------------
| BOOKINGS
|--------------------------------------------------------------------------
*/

$bookings = mysqli_query($conn, "
    SELECT b.*, s.name as service_name
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    $where
    ORDER BY booking_date ASC, booking_time ASC
");

$today = date("Y-m-d");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Appointment System</title>

    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
            padding: 30px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
        }

        button {
            padding: 10px 15px;
            background: #222;
            color: white;
            border: none;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #eee;
        }

        .today {
            background: #fff3cd;
        }

        .cancel {
            color: red;
        }

        .error {
            color: red;
        }

        .filter {
            display: flex;
            gap: 10px;
        }
    </style>

</head>

<body>

    <div class="container">

        <h1>Appointment Booking System</h1>

        <!-- ERRORS -->
        <?php foreach ($errors as $e): ?>
            <p class="error"><?php echo $e; ?></p>
        <?php endforeach; ?>

        <!-- BOOKING FORM -->
        <form method="POST">

            <select name="service_id" required>
                <option value="">Select Service</option>
                <option value="AD">AS</option>
                <?php while ($s = mysqli_fetch_assoc($services)): ?>
                    <option value="<?php echo $s["id"]; ?>">
                        <?php echo $s["name"]; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <input type="text" name="name" placeholder="Name" required>

            <input type="email" name="email" placeholder="Email" required>

            <input type="text" name="phone" placeholder="Phone" required>

            <input type="date" name="date" required>

            <select name="time" required>

                <option value="">Select Time</option>

                <?php
                for ($h = 9; $h <= 17; $h++) {

                    $slot = sprintf("%02d:00", $h);

                    echo "<option value='$slot'>$slot</option>";
                }
                ?>

            </select>

            <button name="book">Book Appointment</button>

        </form>

        <hr>

        <!-- FILTER -->
        <form method="GET" class="filter">

            <input type="date" name="filter_date">

            <select name="filter_service">
                <option value="">All Services</option>

                <?php
                $services2 = mysqli_query($conn, "SELECT * FROM services");
                while ($s = mysqli_fetch_assoc($services2)):
                ?>
                    <option value="<?php echo $s["id"]; ?>">
                        <?php echo $s["name"]; ?>
                    </option>
                <?php endwhile; ?>

            </select>

            <button>Filter</button>

        </form>

        <!-- BOOKINGS -->
        <table>

            <tr>
                <th>Service</th>
                <th>Name</th>
                <th>Email</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php while ($b = mysqli_fetch_assoc($bookings)): ?>

                <tr class="<?php echo ($b["booking_date"] == $today) ? 'today' : ''; ?>">

                    <td><?php echo $b["service_name"]; ?></td>

                    <td><?php echo $b["customer_name"]; ?></td>

                    <td><?php echo $b["customer_email"]; ?></td>

                    <td><?php echo $b["booking_date"]; ?></td>

                    <td><?php echo $b["booking_time"]; ?></td>

                    <td><?php echo $b["status"]; ?></td>

                    <td>

                        <?php if ($b["status"] == "active"): ?>

                            <a class="cancel"
                                href="?cancel=<?php echo $b["id"]; ?>"
                                onclick="return confirm('Cancel booking?')">

                                Cancel

                            </a>

                        <?php endif; ?>

                    </td>

                </tr>

            <?php endwhile; ?>

        </table>

    </div>

</body>

</html>