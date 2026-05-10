<?php

$vowels = 0;
$consonants = 0;
$totalLetters = 0;
$text = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	$text = $_POST["text"];

	$letters = str_split($text);

	foreach ($letters as $char) {

		if (ctype_alpha($char)) {

			$totalLetters++;

			$char = strtolower($char);

			if (in_array($char, ['a', 'e', 'i', 'o', 'u'])) {

				$vowels++;
			} else {

				$consonants++;
			}
		}
	}
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Vowel and Consonant Counter</title>

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
			max-width: 700px;
			margin: auto;
			background: white;
			padding: 25px;
			border-radius: 10px;
		}

		h1 {
			margin-bottom: 20px;
		}

		textarea {
			width: 100%;
			height: 180px;
			padding: 12px;
			resize: none;
			margin-bottom: 15px;
			font-size: 16px;
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
			margin-top: 25px;
			padding: 20px;
			background: #eee;
			border-radius: 8px;
		}

		.result p {
			margin-bottom: 10px;
			font-size: 18px;
		}
	</style>

</head>

<body>

	<div class="container">

		<h1>Vowel and Consonant Counter</h1>

		<form method="POST">

			<textarea
				name="text"
				placeholder="Enter text here..."
				required><?php echo htmlspecialchars($text); ?></textarea>

			<button type="submit">
				Analyze Text
			</button>

		</form>

		<?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>

			<div class="result">

				<p>
					<strong>Total Vowels:</strong>
					<?php echo $vowels; ?>
				</p>

				<p>
					<strong>Total Consonants:</strong>
					<?php echo $consonants; ?>
				</p>

				<p>
					<strong>Total Letters:</strong>
					<?php echo $totalLetters; ?>
				</p>

			</div>

		<?php endif; ?>

	</div>

</body>

</html>