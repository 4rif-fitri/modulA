<?php

require("fpdf/fpdf.php");

class PDF extends FPDF
{
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont("Arial", "I", 10);
        $this->Cell(0, 10, "Page " . $this->PageNo(), 0, 0, "C");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = $_POST["title"];
    $author = $_POST["author"];
    $content = $_POST["content"];

    $pdf = new PDF();
    $pdf->AddPage();

    /*
    |--------------------------------------------------------------------------
    | TITLE
    |--------------------------------------------------------------------------
    */

    $pdf->SetFont("Arial", "B", 18);
    $pdf->Cell(0, 10, $title, 0, 1, "C");

    $pdf->Ln(5);

    /*
    |--------------------------------------------------------------------------
    | AUTHOR
    |--------------------------------------------------------------------------
    */

    $pdf->SetFont("Arial", "", 12);
    $pdf->Cell(0, 10, "Author: " . $author, 0, 1, "C");

    $pdf->Ln(10);

    /*
    |--------------------------------------------------------------------------
    | CONTENT (PARAGRAPHS)
    |--------------------------------------------------------------------------
    */

    $pdf->SetFont("Arial", "", 12);

    $paragraphs = explode("\n", $content);

    foreach ($paragraphs as $p) {

        $p = trim($p);

        if ($p != "") {
            $pdf->MultiCell(0, 8, $p);
            $pdf->Ln(3);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | OUTPUT PDF DOWNLOAD
    |--------------------------------------------------------------------------
    */

    $pdf->Output("D", "report.pdf");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PDF Report Generator</title>

    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
            padding: 40px;
        }

        .container {
            max-width: 700px;
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

        textarea {
            height: 200px;
            resize: none;
        }

        button {
            padding: 12px 18px;
            background: #222;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background: #444;
        }
    </style>

</head>

<body>

    <div class="container">

        <h1>PDF Report Generator</h1>

        <form method="POST">

            <input type="text" name="title" placeholder="Report Title" required>

            <input type="text" name="author" placeholder="Author Name" required>

            <textarea name="content" placeholder="Report Content (use new lines for paragraphs)" required></textarea>

            <button type="submit">Generate PDF</button>

        </form>

    </div>

</body>

</html>