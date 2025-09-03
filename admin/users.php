<?php
include '../main/nav.php';

if (!$isloggedin) {
    header("Location: /");
    exit;
}

if (!$user->isAdmin) {
    echo "<h2>Access Denied</h2><p>You do not have permission to access this page.</p>";
    exit;
}

$stmt = $db->prepare("SELECT * FROM users ORDER BY id DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - NUGLOX Admin</title>
    <link rel="stylesheet" href="/css/admin.css">
    <style>
        body { font-family: Verdana, Geneva, sans-serif; background-color: #f0f0f0; margin:0; padding:0; }
        #admin-container { width: 1000px; margin: 20px auto; }
        h1 { color: #003366; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 10px; border: 1px solid #999; text-align: left; }
        th { background: #0066cc; color: white; }
        tr:nth-child(even) { background: #f2f2f2; }
        a.button-like { padding: 6px 12px; background: #0066cc; color: #fff; text-decoration: none; border-radius: 4px; }
        a.button-like:hover { background: #0055aa; }
    </style>
</head>
<body>
<div id="admin-container">
    <h1>Manage Users</h1>
    <a class="button-like" href="create-user.php">Add New User</a>
    <br><br>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Is Banned</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($users as $u): ?>
        <tr>
            <td><?= htmlspecialchars($u['id']) ?></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['isbanned']) ?></td>
            <td><?= htmlspecialchars($u['joined']) ?></td>
            <td>
                <a class="button-like" href="edit-user.php?id=<?= $u['id'] ?>">Edit</a>
                
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
