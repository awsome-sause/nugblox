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

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$u = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$u) {
    echo "<h2>User not found</h2>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $isBanned = isset($_POST['isBanned']) ? 1 : 0;
    $banReason = $_POST['banReason'] ?? '';
	
	if(!empty($username)){
			$stmt = $db->prepare("INSERT INTO prevusernames (previoususername, userid) VALUES (?, ?)");
			$stmt->execute([$u['username'], $u['id']]);
	}

    $stmt = $db->prepare("UPDATE users SET username = ?, email = ?, isbanned = ?, bannote = ? WHERE id = ?");
    $stmt->execute([$username, $email, $isBanned, $banReason, $id]);

    header("Location: users.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User - NUGLOX Admin</title>
    <link rel="stylesheet" href="/css/admin.css">
    <style>
        body { font-family: Verdana, Geneva, sans-serif; background-color: #f0f0f0; margin:0; padding:0; }
        #admin-container { width: 600px; margin: 20px auto; }
        h1 { color: #003366; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type=text], input[type=email], textarea { width: 100%; padding: 6px; margin-top: 4px; }
        input[type=submit] { margin-top: 15px; padding: 8px 16px; background: #0066cc; color: white; border: none; cursor: pointer; }
        input[type=submit]:hover { background: #0055aa; }
    </style>
</head>
<body>
<div id="admin-container">
    <h1>Edit User</h1>
    <form method="post">
        <label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($u['username']) ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($u['email']) ?>" required>

        <label>
            <input type="checkbox" name="isBanned" <?= $u['isbanned'] ? 'checked' : '' ?>> Banned
        </label>

        <label>Ban Reason</label>
        <textarea name="banReason"><?= htmlspecialchars($u['bannote']) ?></textarea>

        <input type="submit" value="Update User">
    </form>
</div>
</body>
</html>
