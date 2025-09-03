<?php
require_once($_SERVER['DOCUMENT_ROOT']."/main/config.php");

if (!$isloggedin || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    exit;
}

$action = $_POST['action'] ?? '';
$requester_id = (int) ($_POST['requester_id'] ?? 0);

if (!in_array($action, ['accept', 'decline', 'unfriend']) || !$requester_id) {
    http_response_code(400);
    exit;
}

if ($action === 'accept') {
    $stmt = $db->prepare("UPDATE friends SET are_friends = 1 WHERE user_from = ? AND user_to = ? AND are_friends = 0");
    $stmt->execute([$requester_id, $user->id]);

} elseif ($action === 'decline') {
    $stmt = $db->prepare("DELETE FROM friends WHERE user_from = ? AND user_to = ? AND are_friends = 0");
    $stmt->execute([$requester_id, $user->id]);

} elseif ($action === 'unfriend') {
    $stmt = $db->prepare("DELETE FROM friends WHERE 
        (user_from = ? AND user_to = ?) OR 
        (user_from = ? AND user_to = ?) AND are_friends = 1");
    $stmt->execute([$requester_id, $user->id, $user->id, $requester_id]);
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>
