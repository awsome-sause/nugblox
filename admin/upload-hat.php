<?php
include '../main/nav.php';

if (!$isloggedin) {
    header("Location: /");
    exit;
}

if (!$user->isAdmin) {
    echo "<h2>Access Denied</h2>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $price = (int)($_POST['price'] ?? 0);

    if (!$name || !isset($_FILES['mesh']) || !isset($_FILES['thumbnail'])) {
        $error = "Please provide a name, OBJ file, and PNG image.";
    } else {
        $meshMime = $_FILES['mesh']['type'];
        $thumbMime = $_FILES['thumbnail']['type'];

        $meshExt = strtolower(pathinfo($_FILES['mesh']['name'], PATHINFO_EXTENSION));
        $thumbExt = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));

        $allowedMesh = ($meshExt === 'obj' && in_array($meshMime, ['application/octet-stream','text/plain']));
        $allowedThumb = ($thumbExt === 'png' && $thumbMime === 'image/png');

        if (!$allowedMesh || !$allowedThumb) {
            $error = "Invalid file type. Only valid OBJ and PNG files are allowed.";
        } else {
            $stmt = $db->prepare("INSERT INTO catalog (name, description, price, type, creatorid, created) VALUES (?, ?, ?, 'Hat', ?, NOW())");
            $stmt->execute([$name, $desc, $price, $user->id]);
            $itemId = $db->lastInsertId();

            $storagePath = $_SERVER['DOCUMENT_ROOT']."/catalog_storage/hats/";
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0777, true);
            }

            $objPath = $storagePath . $itemId . ".obj";
            move_uploaded_file($_FILES['mesh']['tmp_name'], $objPath);

            $pngPath = $storagePath . $itemId . ".png";
            move_uploaded_file($_FILES['thumbnail']['tmp_name'], $pngPath);

            header("Location: /rendering/hat.php?id=" . $itemId);
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Hat - NUGLOX</title>
    <style>
        body { font-family: Verdana, sans-serif; background:#f0f0f0; }
        #upload-box { width: 500px; margin: 40px auto; background:#fff; padding:20px; border:1px solid #ccc; }
        label { display:block; margin-top:10px; font-weight:bold; }
        input[type=text], textarea { width:100%; padding:6px; margin-top:4px; }
        input[type=number] { padding:6px; width:120px; }
        input[type=file] { margin-top:6px; }
        input[type=submit] { margin-top:15px; padding:8px 16px; background:#0066cc; color:#fff; border:none; cursor:pointer; }
        input[type=submit]:hover { background:#004c99; }
        .error { color:#cc0000; font-weight:bold; margin-bottom:10px; }
    </style>
</head>
<body>
<div id="upload-box">
    <h2>Upload Hat</h2>
    <?php if(!empty($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="post" enctype="multipart/form-data">
        <label>Hat Name</label>
        <input type="text" name="name" required>

        <label>Description</label>
        <textarea name="description"></textarea>

        <label>Price</label>
        <input type="number" name="price" min="0" value="0">

        <label>Hat OBJ File</label>
        <input type="file" name="mesh" accept=".obj" required>

        <label>Hat PNG</label>
        <input type="file" name="thumbnail" accept="image/png" required>

        <input type="submit" value="Upload Hat">
    </form>
</div>
</body>
</html>
