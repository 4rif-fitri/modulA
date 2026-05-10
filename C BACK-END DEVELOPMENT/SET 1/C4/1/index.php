<?php

session_start();

/*
|--------------------------------------------------------------------------
| DATABASE
|--------------------------------------------------------------------------
*/

$host = "localhost";
$user = "root";
$pass = "";
$db = "poll_system";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database connection failed");
}

$ip = $_SERVER["REMOTE_ADDR"];

/*
|--------------------------------------------------------------------------
| CREATE POLL
|--------------------------------------------------------------------------
*/

if (isset($_POST["create_poll"])) {

    $question = trim($_POST["question"]);

    $options = [
        trim($_POST["option1"]),
        trim($_POST["option2"]),
        trim($_POST["option3"]),
        trim($_POST["option4"])
    ];

    if ($question != "") {

        mysqli_query($conn, "
            INSERT INTO polls(question)
            VALUES('$question')
        ");

        $pollId = mysqli_insert_id($conn);

        foreach ($options as $opt) {

            if ($opt != "") {

                mysqli_query($conn, "
                    INSERT INTO options(poll_id, option_text)
                    VALUES($pollId, '$opt')
                ");
            }
        }
    }

    header("Location: " . $_SERVER["PHP_SELF"]);
    exit();
}

/*
|--------------------------------------------------------------------------
| DELETE POLL
|--------------------------------------------------------------------------
*/

if (isset($_GET["delete_poll"])) {

    $pollId = (int)$_GET["delete_poll"];

    mysqli_query($conn, "
        DELETE FROM votes 
        WHERE poll_id = $pollId
    ");

    mysqli_query($conn, "
        DELETE FROM options 
        WHERE poll_id = $pollId
    ");

    mysqli_query($conn, "
        DELETE FROM polls 
        WHERE id = $pollId
    ");

    header("Location: " . $_SERVER["PHP_SELF"]);
    exit();
}

/*
|--------------------------------------------------------------------------
| VOTE
|--------------------------------------------------------------------------
*/

if (isset($_POST["vote"])) {

    $pollId = (int)$_POST["poll_id"];
    $optionId = (int)$_POST["option_id"];

    $sessionKey = "poll_" . $pollId;

    $checkIp = mysqli_query($conn, "
        SELECT * FROM votes
        WHERE poll_id = $pollId
        AND ip_address = '$ip'
    ");

    if (
        !isset($_SESSION[$sessionKey]) &&
        mysqli_num_rows($checkIp) == 0
    ) {

        mysqli_query($conn, "
            UPDATE options
            SET votes = votes + 1
            WHERE id = $optionId
        ");

        mysqli_query($conn, "
            INSERT INTO votes(poll_id, option_id, ip_address)
            VALUES($pollId, $optionId, '$ip')
        ");

        $_SESSION[$sessionKey] = true;
    }

    header("Location: ?results=" . $pollId);
    exit();
}

/*
|--------------------------------------------------------------------------
| POLLS
|--------------------------------------------------------------------------
*/

$polls = mysqli_query($conn, "
    SELECT * FROM polls
    ORDER BY created_at DESC
");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poll Voting System</title>

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
            max-width: 1000px;
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
            margin-bottom: 12px;
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

        .poll {
            border: 1px solid #ccc;
            padding: 20px;
            margin-top: 20px;
            border-radius: 10px;
        }

        .option {
            margin-bottom: 10px;
        }

        .bar-container {
            width: 100%;
            background: #ddd;
            border-radius: 5px;
            overflow: hidden;
            margin-top: 5px;
        }

        .bar {
            height: 25px;
            background: green;
            text-align: center;
            color: white;
            line-height: 25px;
        }

        .actions {
            margin-top: 15px;
        }

        .delete {
            color: red;
            text-decoration: none;
            margin-left: 10px;
        }

        .view {
            text-decoration: none;
            color: blue;
        }

        .vote-btn {
            margin-top: 10px;
        }
    </style>

</head>

<body>

    <div class="container">

        <h1>Poll / Voting System</h1>

        <!-- CREATE POLL -->

        <h2>Create Poll</h2>

        <form method="POST">

            <input
                type="text"
                name="question"
                placeholder="Poll Question"
                required>

            <input
                type="text"
                name="option1"
                placeholder="Option 1"
                required>

            <input
                type="text"
                name="option2"
                placeholder="Option 2"
                required>

            <input
                type="text"
                name="option3"
                placeholder="Option 3"
                required>

            <input
                type="text"
                name="option4"
                placeholder="Option 4"
                required>

            <button type="submit" name="create_poll">
                Create Poll
            </button>

        </form>

        <hr style="margin:30px 0;">

        <!-- POLL LIST -->

        <h2>All Polls</h2>

        <?php while ($poll = mysqli_fetch_assoc($polls)): ?>

            <div class="poll">

                <h3>
                    <?php echo htmlspecialchars($poll["question"]); ?>
                </h3>

                <p>
                    Created:
                    <?php echo $poll["created_at"]; ?>
                </p>

                <div class="actions">

                    <a
                        class="view"
                        href="?vote=<?php echo $poll["id"]; ?>">

                        Vote

                    </a>

                    |

                    <a
                        class="view"
                        href="?results=<?php echo $poll["id"]; ?>">

                        View Results

                    </a>

                    |

                    <a
                        class="delete"
                        href="?delete_poll=<?php echo $poll["id"]; ?>"
                        onclick="return confirm('Delete this poll?')">

                        Delete

                    </a>

                </div>

            </div>

        <?php endwhile; ?>

        <!-- VOTE PAGE -->

        <?php if (isset($_GET["vote"])): ?>

            <?php

            $pollId = (int)$_GET["vote"];

            $poll = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT * FROM polls
            WHERE id = $pollId
        "));

            $options = mysqli_query($conn, "
            SELECT * FROM options
            WHERE poll_id = $pollId
        ");

            ?>

            <hr style="margin:30px 0;">

            <h2>Vote Poll</h2>

            <div class="poll">

                <h3>
                    <?php echo $poll["question"]; ?>
                </h3>

                <form method="POST">

                    <input
                        type="hidden"
                        name="poll_id"
                        value="<?php echo $pollId; ?>">

                    <?php while ($opt = mysqli_fetch_assoc($options)): ?>

                        <div class="option">

                            <label>

                                <input
                                    type="radio"
                                    name="option_id"
                                    value="<?php echo $opt["id"]; ?>"
                                    required>

                                <?php echo $opt["option_text"]; ?>

                            </label>

                        </div>

                    <?php endwhile; ?>

                    <button
                        class="vote-btn"
                        type="submit"
                        name="vote">

                        Submit Vote

                    </button>

                </form>

            </div>

        <?php endif; ?>

        <!-- RESULTS -->

        <?php if (isset($_GET["results"])): ?>

            <?php

            $pollId = (int)$_GET["results"];

            $poll = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT * FROM polls
            WHERE id = $pollId
        "));

            $options = mysqli_query($conn, "
            SELECT * FROM options
            WHERE poll_id = $pollId
        ");

            $totalVotes = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT SUM(votes) as total
            FROM options
            WHERE poll_id = $pollId
        "))["total"];

            ?>

            <hr style="margin:30px 0;">

            <h2>Poll Results</h2>

            <div class="poll">

                <h3>
                    <?php echo $poll["question"]; ?>
                </h3>

                <p>
                    Total Votes:
                    <?php echo $totalVotes ?? 0; ?>
                </p>

                <br>

                <?php while ($opt = mysqli_fetch_assoc($options)): ?>

                    <?php

                    $votes = $opt["votes"];

                    $percentage = 0;

                    if ($totalVotes > 0) {
                        $percentage = round(($votes / $totalVotes) * 100);
                    }

                    ?>

                    <div class="option">

                        <strong>
                            <?php echo $opt["option_text"]; ?>
                        </strong>

                        (<?php echo $votes; ?> votes)

                        <div class="bar-container">

                            <div
                                class="bar"
                                style="width: <?php echo $percentage; ?>%;">

                                <?php echo $percentage; ?>%

                            </div>

                        </div>

                    </div>

                <?php endwhile; ?>

            </div>

        <?php endif; ?>

    </div>

</body>

</html>