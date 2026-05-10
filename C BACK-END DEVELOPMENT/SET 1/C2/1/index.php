<?php

$colors = [];

function hexToRgb($hex)
{
    $hex = str_replace("#", "", $hex);

    if (strlen($hex) == 3) {
        $hex =
            $hex[0] . $hex[0] .
            $hex[1] . $hex[1] .
            $hex[2] . $hex[2];
    }

    return [
        hexdec(substr($hex, 0, 2)),
        hexdec(substr($hex, 2, 2)),
        hexdec(substr($hex, 4, 2))
    ];
}

function rgbToHex($r, $g, $b)
{
    return sprintf("#%02X%02X%02X", $r, $g, $b);
}

function adjustBrightness($hex, $percent)
{
    list($r, $g, $b) = hexToRgb($hex);

    $r = max(0, min(255, $r + ($r * $percent)));
    $g = max(0, min(255, $g + ($g * $percent)));
    $b = max(0, min(255, $b + ($b * $percent)));

    return rgbToHex($r, $g, $b);
}

function complementaryColor($hex)
{
    list($r, $g, $b) = hexToRgb($hex);

    return rgbToHex(
        255 - $r,
        255 - $g,
        255 - $b
    );
}

function randomColor()
{
    return sprintf(
        "#%06X",
        mt_rand(0, 0xFFFFFF)
    );
}

$baseColor = "#3498db";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $baseColor = $_POST["color"];

    $colors = [
        "Original" => $baseColor,
        "Lighter (+30%)" => adjustBrightness($baseColor, 0.3),
        "Darker (-30%)" => adjustBrightness($baseColor, -0.3),
        "Complementary" => complementaryColor($baseColor),
        "Random Accent" => randomColor()
    ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Palette Generator</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            padding: 40px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        h1 {
            margin-bottom: 20px;
        }

        form {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 30px;
        }

        input[type=color] {
            width: 80px;
            height: 50px;
            border: none;
            cursor: pointer;
        }

        button {
            padding: 12px 20px;
            border: none;
            background: #222;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #444;
        }

        .palette {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .card {
            text-align: center;
            width: 180px;
        }

        .swatch {
            width: 80px;
            height: 80px;
            margin: auto;
            border-radius: 8px;
            border: 2px solid #ccc;
            margin-bottom: 10px;
        }

        .label {
            font-weight: bold;
            margin-bottom: 8px;
        }

        .info {
            font-size: 14px;
            margin-top: 5px;
        }

        @media(max-width:768px) {

            .palette {
                justify-content: center;
            }

        }
    </style>

</head>

<body>

    <div class="container">

        <h1>Color Palette Generator</h1>

        <form method="POST">

            <input
                type="color"
                name="color"
                value="<?php echo $baseColor; ?>"
                required>

            <button type="submit">
                Generate Palette
            </button>

        </form>

        <?php if (!empty($colors)): ?>

            <div class="palette">

                <?php foreach ($colors as $label => $hex): ?>

                    <?php
                    list($r, $g, $b) = hexToRgb($hex);
                    ?>

                    <div class="card">

                        <div
                            class="swatch"
                            style="background: <?php echo $hex; ?>;">
                        </div>

                        <div class="label">
                            <?php echo $label; ?>
                        </div>

                        <div class="info">
                            HEX: <?php echo $hex; ?>
                        </div>

                        <div class="info">
                            RGB:
                            <?php echo "rgb($r, $g, $b)"; ?>
                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</body>

</html>