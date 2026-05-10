<?php

$result = "";
$error = "";
$equation = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $num1 = $_POST["num1"];
    $num2 = $_POST["num2"];
    $op = $_POST["operation"];

    if (!is_numeric($num1) || !is_numeric($num2)) {
        $error = "Inputs must be valid numbers.";
    } else {

        $num1 = (float)$num1;
        $num2 = (float)$num2;

        switch ($op) {

            case "add":
                $result = $num1 + $num2;
                $equation = "$num1 + $num2 = $result";
                break;

            case "subtract":
                $result = $num1 - $num2;
                $equation = "$num1 - $num2 = $result";
                break;

            case "multiply":
                $result = $num1 * $num2;
                $equation = "$num1 × $num2 = $result";
                break;

            case "divide":

                if ($num2 == 0) {
                    $error = "Division by zero is not allowed.";
                } else {
                    $result = $num1 / $num2;
                    $equation = "$num1 ÷ $num2 = $result";
                }

                break;

            default:
                $error = "Invalid operation selected.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Calculator</title>

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
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        h1 {
            margin-bottom: 20px;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 12px;
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

        .result {
            margin-top: 20px;
            padding: 15px;
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 5px;
        }

        .error {
            margin-top: 20px;
            padding: 15px;
            background: #ffebee;
            color: #c62828;
            border-radius: 5px;
        }
    </style>
</head>

<body>

    <div class="container">

        <h1>Simple Calculator</h1>

        <form method="POST">

            <input type="text" name="num1" placeholder="First number" required>

            <input type="text" name="num2" placeholder="Second number" required>

            <select name="operation" required>

                <option value="">Select operation</option>
                <option value="add">Add (+)</option>
                <option value="subtract">Subtract (-)</option>
                <option value="multiply">Multiply (×)</option>
                <option value="divide">Divide (÷)</option>

            </select>

            <button type="submit">Calculate</button>

        </form>

        <?php if ($error): ?>

            <div class="error">
                <?php echo $error; ?>
            </div>

        <?php elseif ($equation): ?>

            <div class="result">
                <?php echo $equation; ?>
            </div>

        <?php endif; ?>

    </div>

</body>

</html>