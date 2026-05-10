<?php
$result = [];
$totalWords = 0;
$topWords = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $text = strtolower(trim($_POST["text"]));

    // Remove punctuation
    $text = preg_replace("/[^\w\s]/", "", $text);

    // Convert text into array
    $words = preg_split("/\s+/", $text);

    // Remove empty values
    $words = array_filter($words);

    // Count total words
    $totalWords = count($words);

    // Count frequency
    $frequency = array_count_values($words);

    // Sort highest to lowest
    arsort($frequency);

    $result = $frequency;

    // Top 10 words
    $topWords = array_slice($frequency, 0, 10, true);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Word Frequency Counter</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 30px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        textarea {
            width: 100%;
            height: 180px;
            padding: 10px;
            resize: none;
            margin-bottom: 15px;
            font-size: 16px;
        }

        button {
            padding: 10px 20px;
            border: none;
            background: #333;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }

        button:hover {
            background: #555;
        }

        h2 {
            margin: 20px 0 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th,
        table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        table th {
            background: #eee;
        }
    </style>
</head>

<body>

    <div class="container">

        <h1>Word Frequency Counter</h1>

        <form method="POST">

            <textarea name="text"
                placeholder="Enter text here..."><?php echo $_POST["text"] ?? ""; ?></textarea>

            <button type="submit">Count Words</button>

        </form>

        <?php if (!empty($result)) : ?>

            <h2>Total Words: <?php echo $totalWords; ?></h2>

            <h2>Top 10 Most Common Words</h2>

            <table>
                <tr>
                    <th>Word</th>
                    <th>Frequency</th>
                </tr>

                <?php foreach ($topWords as $word => $count) : ?>

                    <tr>
                        <td><?php echo $word; ?></td>
                        <td><?php echo $count; ?></td>
                    </tr>

                <?php endforeach; ?>

            </table>

            <h2>All Unique Words</h2>

            <table>
                <tr>
                    <th>Word</th>
                    <th>Frequency</th>
                </tr>

                <?php foreach ($result as $word => $count) : ?>

                    <tr>
                        <td><?php echo $word; ?></td>
                        <td><?php echo $count; ?></td>
                    </tr>

                <?php endforeach; ?>

            </table>

        <?php endif; ?>

    </div>

</body>

</html>