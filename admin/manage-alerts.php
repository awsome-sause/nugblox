<?php
include '../main/nav.php';

if (!$isloggedin || !$user->isAdmin) {
    echo "<h2>Access Denied</h2>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $text = $_POST['text'] ?? '';
        $color = $_POST['color'] ?? '#ffffff';
        if (!empty($text)) {
            $stmt = $db->prepare("INSERT INTO alerts (text, color) VALUES (?, ?)");
            $stmt->execute([$text, $color]);
        }
    } elseif (isset($_POST['delete'])) {
        $id = (int)$_POST['id'];
        $stmt = $db->prepare("DELETE FROM alerts WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

$alerts = $db->query("SELECT * FROM alerts")->fetchAll(PDO::FETCH_ASSOC);
?>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Alerts - Admin</title>
<style>
body { margin:0; padding:0; font-family: Verdana,Geneva,sans-serif; background:#d4d4d4; color:#000; }
#page { width:600px; margin:20px auto; background:#fff; border:1px solid #999; padding:15px; }
h1 { color:#003366; }
input[type=text], input[type=color] { width:100%; padding:6px; margin:4px 0 10px 0; }
input[type=submit] { padding:8px 16px; background:#0066cc; color:white; border:none; cursor:pointer; margin-top:6px; }
input[type=submit]:hover { background:#0055aa; }
.alert-box { padding:8px; margin:6px 0; color:#fff; display:flex; justify-content:space-between; align-items:center; }
.alert-box span { cursor:pointer; padding-left:10px; }
</style>
</head>
<body>
<div id="page">
<h1>Manage Alerts</h1>
<h2>Add Alert</h2>
<form method="post">
    <label>Text</label>
    <input type="text" name="text" required>
    <label>Color</label>
    <input type="color" name="color" value="#ff0000">
    <input type="submit" name="add" value="Add Alert">
</form>
<h2>Existing Alerts</h2>
<?php foreach ($alerts as $a): ?>
<div class="alert-box" style="background-color:<?= htmlspecialchars($a['color']) ?>">
    <span><?= htmlspecialchars($a['text']) ?></span>
    <form method="post" style="margin:0;">
        <input type="hidden" name="id" value="<?= $a['id'] ?>">
        <input type="submit" name="delete" value="✖">
    </form>
</div>
<?php endforeach; ?>
</div>
</body>
</html>
