<?php
require_once($_SERVER['DOCUMENT_ROOT']."/main/config.php");

$user_to = (int)$_POST['user_id'];

if (!$isloggedin || $user_to < 1 || $user->id === $user_to) {
    header("Location: /user.php?id=" . $user_to);
    exit;
}

$user_from = $user->id;

$stmt = $db->prepare("SELECT * FROM friends WHERE (user_from = ? AND user_to = ?) OR (user_from = ? AND user_to = ?)");
$stmt->execute([$user_from, $user_to, $user_to, $user_from]);
$exists = $stmt->fetch();

if ($exists) {
    if ($exists['user_from'] == $user_to && $exists['user_to'] == $user_from && $exists['are_friends'] == 0) {
        $update = $db->prepare("UPDATE friends SET are_friends = 1 WHERE id = ?");
        $update->execute([$exists['id']]);
    }
    header("Location: /user.php?id=" . $user_to);
    exit;
}

$insert = $db->prepare("INSERT INTO friends (user_from, user_to, whenadded, are_friends) VALUES (?, ?, NOW(), 0)");
$insert->execute([$user_from, $user_to]);

header("Location: /user.php?id=" . $user_to);
exit;
?>
