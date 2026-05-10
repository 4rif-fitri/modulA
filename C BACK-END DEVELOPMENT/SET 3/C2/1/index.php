<?php

$table = [];
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_FILES["csv"]) && $_FILES["csv"]["error"] == 0) {

        $fileName = $_FILES["csv"]["name"];
        $fileTmp = $_FILES["csv"]["tmp_name"];
        $fileSize = $_FILES["csv"]["size"];

        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validate file type
        if ($ext !== "csv") {
            $error = "Only CSV files are allowed.";
        }

        // Validate size (1MB)
        elseif ($fileSize > 1024 * 1024) {
            $error = "File size must not exceed 1MB.";
        } else {

            $file = fopen($fileTmp, "r");

            while (($row = fgetcsv($file)) !== false) {
                $table[] = $row;
            }

            fclose($file);
        }
    } else {
        $error = "File upload failed.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSV to HTML Table</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial;
            background: #f2f2f2;
            padding: 40px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        input[type=file] {
            margin-bottom: 15px;
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

        .error {
            margin-top: 15px;
            color: red;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #333;
            color: white;
        }

        tr:nth-child(even) {
            background: #f2f2f2;
        }

        tr:nth-child(odd) {
            background: #ffffff;
        }
    </style>
</head>

<body>

    <div class="container">

        <h1>CSV to HTML Table Converter</h1>

        <form method="POST" enctype="multipart/form-data">

            <input type="file" name="csv" accept=".csv" required>

            <button type="submit">Upload & Convert</button>

        </form>

        <?php if ($error): ?>
            <div class="error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($table)): ?>

            <table>

                <thead>

                    <tr>
                        <?php foreach ($table[0] as $header): ?>
                            <th><?php echo htmlspecialchars($header); ?></th>
                        <?php endforeach; ?>
                    </tr>

                </thead>

                <tbody>

                    <?php for ($i = 1; $i < count($table); $i++): ?>

                        <tr>

                            <?php foreach ($table[$i] as $cell): ?>

                                <td><?php echo htmlspecialchars($cell); ?></td>

                            <?php endforeach; ?>

                        </tr>

                    <?php endfor; ?>

                </tbody>

            </table>

        <?php endif; ?>

    </div>

</body>

</html>