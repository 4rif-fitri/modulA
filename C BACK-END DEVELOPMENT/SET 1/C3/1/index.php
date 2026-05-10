<?php

/*
|--------------------------------------------------------------------------
| FOLDERS
|--------------------------------------------------------------------------
*/

$originalDir = "uploads/original/";
$thumbDir = "uploads/thumbnails/";
$dataFile = "images.json";

if (!file_exists($originalDir)) {
    mkdir($originalDir, 0777, true);
}

if (!file_exists($thumbDir)) {
    mkdir($thumbDir, 0777, true);
}

if (!file_exists($dataFile)) {
    file_put_contents($dataFile, json_encode([]));
}

/*
|--------------------------------------------------------------------------
| LOAD DATA
|--------------------------------------------------------------------------
*/

$images = json_decode(file_get_contents($dataFile), true);

/*
|--------------------------------------------------------------------------
| CREATE THUMBNAIL
|--------------------------------------------------------------------------
*/

function createThumbnail($source, $destination, $mime)
{
    list($width, $height) = getimagesize($source);

    $thumbSize = 200;

    $ratio = min($thumbSize / $width, $thumbSize / $height);

    $newWidth = $width * $ratio;
    $newHeight = $height * $ratio;

    $thumb = imagecreatetruecolor($newWidth, $newHeight);

    switch ($mime) {

        case "image/jpeg":
            $src = imagecreatefromjpeg($source);
            break;

        case "image/png":
            $src = imagecreatefrompng($source);
            break;

        case "image/gif":
            $src = imagecreatefromgif($source);
            break;

        default:
            return false;
    }

    imagecopyresampled(
        $thumb,
        $src,
        0,
        0,
        0,
        0,
        $newWidth,
        $newHeight,
        $width,
        $height
    );

    switch ($mime) {

        case "image/jpeg":
            imagejpeg($thumb, $destination);
            break;

        case "image/png":
            imagepng($thumb, $destination);
            break;

        case "image/gif":
            imagegif($thumb, $destination);
            break;
    }

    imagedestroy($thumb);
    imagedestroy($src);

    return true;
}

/*
|--------------------------------------------------------------------------
| UPLOAD
|--------------------------------------------------------------------------
*/

$message = "";

if (isset($_POST["upload"])) {

    $allowedExt = ["jpg", "jpeg", "png", "gif"];

    $allowedMime = [
        "image/jpeg",
        "image/png",
        "image/gif"
    ];

    foreach ($_FILES["images"]["tmp_name"] as $key => $tmpName) {

        if ($_FILES["images"]["error"][$key] != 0) {
            continue;
        }

        $name = $_FILES["images"]["name"][$key];
        $size = $_FILES["images"]["size"][$key];

        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        $mime = mime_content_type($tmpName);

        if (!in_array($ext, $allowedExt)) {
            $message = "Invalid file extension";
            continue;
        }

        if (!in_array($mime, $allowedMime)) {
            $message = "Invalid MIME type";
            continue;
        }

        if ($size > 2 * 1024 * 1024) {
            $message = "File too large (max 2MB)";
            continue;
        }

        $newName = time() . "_" . uniqid() . "." . $ext;

        $originalPath = $originalDir . $newName;
        $thumbPath = $thumbDir . $newName;

        move_uploaded_file($tmpName, $originalPath);

        createThumbnail($originalPath, $thumbPath, $mime);

        $images[] = [
            "filename" => $newName,
            "date" => date("Y-m-d H:i:s")
        ];
    }

    file_put_contents($dataFile, json_encode($images, JSON_PRETTY_PRINT));

    $message = "Upload completed";

    header("Location: " . $_SERVER["PHP_SELF"]);
    exit();
}

/*
|--------------------------------------------------------------------------
| DELETE
|--------------------------------------------------------------------------
*/

if (isset($_GET["delete"])) {

    $filename = $_GET["delete"];

    foreach ($images as $index => $img) {

        if ($img["filename"] == $filename) {

            $originalPath = $originalDir . $filename;
            $thumbPath = $thumbDir . $filename;

            if (file_exists($originalPath)) {
                unlink($originalPath);
            }

            if (file_exists($thumbPath)) {
                unlink($thumbPath);
            }

            unset($images[$index]);
        }
    }

    $images = array_values($images);

    file_put_contents($dataFile, json_encode($images, JSON_PRETTY_PRINT));

    header("Location: " . $_SERVER["PHP_SELF"]);
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Gallery Upload</title>

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

        h1 {
            margin-bottom: 20px;
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

        .progress {
            width: 100%;
            background: #ddd;
            margin-top: 15px;
            display: none;
            border-radius: 5px;
            overflow: hidden;
        }

        .progress-bar {
            height: 20px;
            width: 0%;
            background: green;
        }

        .gallery {
            margin-top: 30px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        .card {
            background: #fff;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }

        .card img {
            width: 200px;
            height: 200px;
            object-fit: contain;
            border-radius: 5px;
            background: #eee;
        }

        .filename {
            margin-top: 10px;
            word-break: break-all;
        }

        .date {
            margin-top: 5px;
            font-size: 14px;
            color: gray;
        }

        .delete {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 15px;
            background: red;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .delete:hover {
            background: darkred;
        }
    </style>

</head>

<body>

    <div class="container">

        <h1>Image Gallery Upload</h1>

        <form
            method="POST"
            enctype="multipart/form-data"
            id="uploadForm">

            <input
                type="file"
                name="images[]"
                multiple
                accept=".jpg,.jpeg,.png,.gif"
                required>

            <br>

            <button type="submit" name="upload">
                Upload Images
            </button>

            <div class="progress">

                <div class="progress-bar"></div>

            </div>

        </form>

        <div class="gallery">

            <?php foreach ($images as $img): ?>

                <div class="card">

                    <a
                        href="<?php echo $originalDir . $img["filename"]; ?>"
                        target="_blank">

                        <img
                            src="<?php echo $thumbDir . $img["filename"]; ?>"
                            alt="">

                    </a>

                    <div class="filename">
                        <?php echo $img["filename"]; ?>
                    </div>

                    <div class="date">
                        Uploaded:
                        <?php echo $img["date"]; ?>
                    </div>

                    <a
                        class="delete"
                        href="?delete=<?php echo $img["filename"]; ?>"
                        onclick="return confirm('Delete image?')">

                        Delete

                    </a>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

    <script>
        const form = document.getElementById("uploadForm");

        form.addEventListener("submit", function() {

            document.querySelector(".progress").style.display = "block";

            let bar = document.querySelector(".progress-bar");

            let width = 0;

            let interval = setInterval(() => {

                width += 10;

                bar.style.width = width + "%";

                if (width >= 100) {
                    clearInterval(interval);
                }

            }, 100);

        });
    </script>

</body>

</html>