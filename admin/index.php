<?php
include '../main/nav.php';

if (!$isloggedin) {
    header("Location: /");
    exit;
}

if (!$user->isAdmin == 1) {
    echo "<h2>Access Denied</h2><p>You do not have permission to access this page.</p>";
    exit;
}

$stmtUsers = $db->prepare("SELECT COUNT(*) AS total FROM users");
$stmtUsers->execute();
$totalUsers = $stmtUsers->fetch(PDO::FETCH_ASSOC)['total'];

$stmtGames = $db->prepare("SELECT COUNT(*) AS total FROM games");
$stmtGames->execute();
$totalGames = $stmtGames->fetch(PDO::FETCH_ASSOC)['total'];

$stmtItems = $db->prepare("SELECT COUNT(*) AS total FROM catalog");
$stmtItems->execute();
$totalItems = $stmtItems->fetch(PDO::FETCH_ASSOC)['total'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NUGLOX Admin Panel</title>
    <link rel="stylesheet" href="/css/admin.css">
    <style>
        body { font-family: Verdana, Geneva, sans-serif; background-color: #f0f0f0; margin:0; padding:0; }
        #admin-container { width: 1000px; margin: 20px auto; }
        h1 { color: #003366; }
        .dashboard { display: flex; gap: 15px; flex-wrap: wrap; }
        .card { flex: 1 1 200px; background: white; padding: 15px; border: 1px solid #999; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .card h3 { margin: 0 0 10px 0; color: #0066cc; }
        .card p { font-size: 18px; font-weight: bold; }
        .nav-links { margin-bottom: 20px; }
        .nav-links a { margin-right: 15px; color: #fff; text-decoration: none; background: #0066cc; padding: 6px 12px; border-radius: 4px; }
        .nav-links a:hover { background: #0055aa; }
    </style>
</head>
<body>
    <div id="admin-container">
        <h1>NUGLOX Admin Panel</h1>
        <div class="nav-links">
            <a href="users.php">Manage Users</a>
            <a href="games.php">Manage Games (W.I.P)</a>
            <a href="items.php">Manage Catalog (W.I.P)</a>
			<a href="manage-alerts.php">Manage Alerts</a>
        </div>

        <div class="dashboard">
            <div class="card">
                <h3>Total Users</h3>
                <p><?= htmlspecialchars($totalUsers) ?></p>
            </div>
            <div class="card">
                <h3>Total Games</h3>
                <p><?= htmlspecialchars($totalGames) ?></p>
            </div>
            <div class="card">
                <h3>Total Items</h3>
                <p><?= htmlspecialchars($totalItems) ?></p>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <h2>NUGLOX Account Usage</h2>
            <a class="button-like" href="upload-hat.php">Add New Hat</a>
            <a class="button-like" href="upload-face.php">Add New Face</a>
        </div>
    </div>
</body>
</html>
