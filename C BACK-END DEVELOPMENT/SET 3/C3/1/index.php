<?php

/*
|--------------------------------------------------------------------------
| DATABASE CONNECTION
|--------------------------------------------------------------------------
*/

$host = "localhost";
$user = "root";
$pass = "";
$db = "blog_system";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database connection failed");
}

/*
|--------------------------------------------------------------------------
| CREATE POST
|--------------------------------------------------------------------------
*/

if (isset($_POST["create_post"])) {

    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);

    if ($title != "" && $content != "") {

        $stmt = mysqli_prepare($conn, "
            INSERT INTO posts(title, content)
            VALUES(?, ?)
        ");

        mysqli_stmt_bind_param($stmt, "ss", $title, $content);
        mysqli_stmt_execute($stmt);
    }

    header("Location: " . $_SERVER["PHP_SELF"]);
    exit();
}

/*
|--------------------------------------------------------------------------
| DELETE POST
|--------------------------------------------------------------------------
*/

if (isset($_GET["delete_post"])) {

    $id = (int)$_GET["delete_post"];

    mysqli_query($conn, "DELETE FROM comments WHERE post_id = $id");
    mysqli_query($conn, "DELETE FROM posts WHERE id = $id");

    header("Location: " . $_SERVER["PHP_SELF"]);
    exit();
}

/*
|--------------------------------------------------------------------------
| ADD COMMENT
|--------------------------------------------------------------------------
*/

if (isset($_POST["add_comment"])) {

    $postId = (int)$_POST["post_id"];
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $comment = trim($_POST["comment"]);

    if (
        $name != "" &&
        $email != "" &&
        $comment != "" &&
        filter_var($email, FILTER_VALIDATE_EMAIL) &&
        strlen($comment) >= 10
    ) {

        $stmt = mysqli_prepare($conn, "
            INSERT INTO comments
            (post_id, name, email, comment_text)
            VALUES (?, ?, ?, ?)
        ");

        mysqli_stmt_bind_param(
            $stmt,
            "isss",
            $postId,
            $name,
            $email,
            $comment
        );

        mysqli_stmt_execute($stmt);
    }

    header("Location: ?view=" . $postId);
    exit();
}

/*
|--------------------------------------------------------------------------
| VIEW POST
|--------------------------------------------------------------------------
*/

$viewPost = null;
$comments = [];

if (isset($_GET["view"])) {

    $postId = (int)$_GET["view"];

    $res = mysqli_query($conn, "
        SELECT * FROM posts
        WHERE id = $postId
    ");

    $viewPost = mysqli_fetch_assoc($res);

    $comments = mysqli_query($conn, "
        SELECT * FROM comments
        WHERE post_id = $postId
        ORDER BY created_at DESC
    ");
}

/*
|--------------------------------------------------------------------------
| ALL POSTS
|--------------------------------------------------------------------------
*/

$posts = mysqli_query($conn, "
    SELECT p.*,
    (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) as comment_count
    FROM posts p
    ORDER BY created_at DESC
");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Blog System</title>

    <style>
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

        input,
        textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 10px;
        }

        button {
            padding: 10px 15px;
            background: #222;
            color: white;
            border: none;
            cursor: pointer;
        }

        .post {
            background: #eee;
            padding: 15px;
            margin-top: 15px;
            border-radius: 8px;
        }

        a {
            color: blue;
            text-decoration: none;
        }

        .comment {
            background: #f9f9f9;
            padding: 10px;
            margin-top: 10px;
            border-left: 3px solid #333;
        }

        .small {
            font-size: 13px;
            color: gray;
        }
    </style>

</head>

<body>

    <div class="container">

        <h1>Blog System</h1>

        <!-- CREATE POST -->
        <h2>Create Post</h2>

        <form method="POST">

            <input type="text" name="title" placeholder="Title" required>

            <textarea name="content" placeholder="Content" required></textarea>

            <button name="create_post">Create</button>

        </form>

        <hr>

        <!-- VIEW SINGLE POST -->
        <?php if ($viewPost): ?>

            <h2><?php echo htmlspecialchars($viewPost["title"]); ?></h2>

            <p class="small">
                <?php echo $viewPost["created_at"]; ?>
            </p>

            <p>
                <?php echo nl2br(htmlspecialchars($viewPost["content"])); ?>
            </p>

            <hr>

            <h3>Comments</h3>

            <!-- COMMENT FORM -->
            <form method="POST">

                <input type="hidden" name="post_id" value="<?php echo $viewPost["id"]; ?>">

                <input type="text" name="name" placeholder="Name" required>

                <input type="email" name="email" placeholder="Email" required>

                <textarea name="comment" placeholder="Comment (min 10 chars)" required></textarea>

                <button name="add_comment">Add Comment</button>

            </form>

            <!-- COMMENT LIST -->
            <?php while ($c = mysqli_fetch_assoc($comments)): ?>

                <div class="comment">

                    <b><?php echo htmlspecialchars($c["name"]); ?></b>

                    <p><?php echo htmlspecialchars($c["comment_text"]); ?></p>

                    <span class="small"><?php echo $c["created_at"]; ?></span>

                </div>

            <?php endwhile; ?>

            <a href="index.php">Back</a>

        <?php else: ?>

            <!-- POST LIST -->
            <h2>All Posts</h2>

            <?php while ($p = mysqli_fetch_assoc($posts)): ?>

                <div class="post">

                    <h3><?php echo htmlspecialchars($p["title"]); ?></h3>

                    <p class="small">
                        <?php echo $p["created_at"]; ?> |
                        Comments: <?php echo $p["comment_count"]; ?>
                    </p>

                    <p>
                        <?php echo substr(strip_tags($p["content"]), 0, 100); ?>...
                    </p>

                    <a href="?view=<?php echo $p["id"]; ?>">Read More</a> |

                    <a href="?delete_post=<?php echo $p["id"]; ?>"
                        onclick="return confirm('Delete post?')">
                        Delete
                    </a>

                </div>

            <?php endwhile; ?>

        <?php endif; ?>

    </div>

</body>

</html>