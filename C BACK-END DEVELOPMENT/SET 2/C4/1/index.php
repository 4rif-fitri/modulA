<?php

/*
|--------------------------------------------------------------------------
| DATABASE CONNECTION
|--------------------------------------------------------------------------
*/

$host = "localhost";
$user = "root";
$pass = "";
$db = "url_shortener";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database connection failed");
}

/*
|--------------------------------------------------------------------------
| GENERATE RANDOM CODE
|--------------------------------------------------------------------------
*/

function generateCode($length = 6)
{
    $characters =
        "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

    return substr(
        str_shuffle($characters),
        0,
        $length
    );
}

/*
|--------------------------------------------------------------------------
| REDIRECT SHORT URL
|--------------------------------------------------------------------------
*/

if (isset($_GET["code"])) {

    $code = trim($_GET["code"]);

    $stmt = mysqli_prepare($conn, "
        SELECT *
        FROM urls
        WHERE short_code = ?
    ");

    mysqli_stmt_bind_param($stmt, "s", $code);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {

        $row = mysqli_fetch_assoc($result);

        $update = mysqli_prepare($conn, "
            UPDATE urls
            SET clicks = clicks + 1
            WHERE id = ?
        ");

        mysqli_stmt_bind_param(
            $update,
            "i",
            $row["id"]
        );

        mysqli_stmt_execute($update);

        header("Location: " . $row["original_url"]);
        exit();
    } else {

        die("Short URL not found");
    }
}

/*
|--------------------------------------------------------------------------
| CREATE SHORT URL
|--------------------------------------------------------------------------
*/

$message = "";

if (isset($_POST["shorten"])) {

    $url = trim($_POST["url"]);

    /*
    |--------------------------------------------------------------------------
    | VALIDATE URL
    |--------------------------------------------------------------------------
    */

    if (!filter_var($url, FILTER_VALIDATE_URL)) {

        $message = "Invalid URL format";
    } else {

        /*
        |--------------------------------------------------------------------------
        | PREVENT DUPLICATE CODE
        |--------------------------------------------------------------------------
        */

        do {

            $shortCode = generateCode();

            $check = mysqli_prepare($conn, "
                SELECT id
                FROM urls
                WHERE short_code = ?
            ");

            mysqli_stmt_bind_param(
                $check,
                "s",
                $shortCode
            );

            mysqli_stmt_execute($check);

            $result = mysqli_stmt_get_result($check);
        } while (mysqli_num_rows($result) > 0);

        /*
        |--------------------------------------------------------------------------
        | INSERT DATABASE
        |--------------------------------------------------------------------------
        */

        $stmt = mysqli_prepare($conn, "
            INSERT INTO urls
            (
                original_url,
                short_code
            )
            VALUES (?, ?)
        ");

        mysqli_stmt_bind_param(
            $stmt,
            "ss",
            $url,
            $shortCode
        );

        if (mysqli_stmt_execute($stmt)) {

            $message = "Short URL created successfully";
        } else {

            $message = "Failed to create URL";
        }
    }
}

/*
|--------------------------------------------------------------------------
| DELETE URL
|--------------------------------------------------------------------------
*/

if (isset($_GET["delete"])) {

    $id = (int)$_GET["delete"];

    $stmt = mysqli_prepare($conn, "
        DELETE FROM urls
        WHERE id = ?
    ");

    mysqli_stmt_bind_param($stmt, "i", $id);

    mysqli_stmt_execute($stmt);

    header("Location: " . $_SERVER["PHP_SELF"]);
    exit();
}

/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

$search = "";

if (isset($_GET["search"])) {
    $search = trim($_GET["search"]);
}

$searchLike = "%" . $search . "%";

$stmt = mysqli_prepare($conn, "
    SELECT *
    FROM urls
    WHERE original_url LIKE ?
    ORDER BY created_at DESC
");

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $searchLike
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

/*
|--------------------------------------------------------------------------
| ANALYTICS
|--------------------------------------------------------------------------
*/

$totalUrls = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) as total
        FROM urls
    ")
)["total"];

$totalClicks = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT SUM(clicks) as total
        FROM urls
    ")
)["total"];

$mostClicked = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT *
        FROM urls
        ORDER BY clicks DESC
        LIMIT 1
    ")
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>URL Shortener</title>

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

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
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

        .message {
            margin: 15px 0;
            color: green;
            font-weight: bold;
        }

        .analytics {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 30px 0;
        }

        .card {
            background: #eee;
            padding: 20px;
            border-radius: 8px;
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

        .truncate {
            max-width: 350px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .delete {
            color: red;
            text-decoration: none;
        }

        a {
            color: blue;
        }

        @media(max-width:768px) {

            .analytics {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 14px;
            }

        }
    </style>

</head>

<body>

    <div class="container">

        <h1>URL Shortener Service</h1>

        <!-- CREATE URL -->

        <form method="POST">

            <input
                type="text"
                name="url"
                placeholder="Enter long URL..."
                required>

            <button
                type="submit"
                name="shorten">

                Shorten URL

            </button>

        </form>

        <?php if ($message): ?>

            <div class="message">
                <?php echo $message; ?>
            </div>

        <?php endif; ?>

        <!-- ANALYTICS -->

        <div class="analytics">

            <div class="card">

                <h3>Total URLs</h3>

                <p>
                    <?php echo $totalUrls; ?>
                </p>

            </div>

            <div class="card">

                <h3>Total Clicks</h3>

                <p>
                    <?php echo $totalClicks ?? 0; ?>
                </p>

            </div>

            <div class="card">

                <h3>Most Clicked URL</h3>

                <?php if ($mostClicked): ?>

                    <p>
                        <?php echo $mostClicked["short_code"]; ?>
                    </p>

                    <p>
                        <?php echo $mostClicked["clicks"]; ?> clicks
                    </p>

                <?php else: ?>

                    <p>No data</p>

                <?php endif; ?>

            </div>

        </div>

        <!-- SEARCH -->

        <form method="GET">

            <input
                type="text"
                name="search"
                placeholder="Search original URL..."
                value="<?php echo htmlspecialchars($search); ?>">

            <button type="submit">
                Search
            </button>

        </form>

        <!-- URL TABLE -->

        <table>

            <tr>

                <th>ID</th>

                <th>Original URL</th>

                <th>Short URL</th>

                <th>Clicks</th>

                <th>Date</th>

                <th>Action</th>

            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)): ?>

                <tr>

                    <td>
                        <?php echo $row["id"]; ?>
                    </td>

                    <td class="truncate">

                        <?php
                        echo htmlspecialchars(
                            $row["original_url"]
                        );
                        ?>

                    </td>

                    <td>

                        <a
                            target="_blank"
                            href="?code=<?php echo $row["short_code"]; ?>">

                            <?php
                            echo
                            $_SERVER["HTTP_HOST"] .
                                $_SERVER["PHP_SELF"] .
                                "?code=" .
                                $row["short_code"];
                            ?>

                        </a>

                    </td>

                    <td>
                        <?php echo $row["clicks"]; ?>
                    </td>

                    <td>
                        <?php echo $row["created_at"]; ?>
                    </td>

                    <td>

                        <a
                            class="delete"
                            href="?delete=<?php echo $row["id"]; ?>"
                            onclick="return confirm('Delete this URL?')">

                            Delete

                        </a>

                    </td>

                </tr>

            <?php endwhile; ?>

        </table>

    </div>

</body>

</html>