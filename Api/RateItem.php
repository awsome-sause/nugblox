<?php
session_start();
require '../main/config.php';

if (!$isloggedin) {
  http_response_code(401);
  echo "You need to login.";
  exit;
}

$userId = $user->id;
$itemId = isset($_POST['itemid']) ? (int)$_POST['itemid'] : 0;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;

if ($itemId < 1 || $rating < 1 || $rating > 5) {
  http_response_code(400);
  echo "Invalid input";
  exit;
}

$stmt = $db->prepare("INSERT INTO item_ratings (itemid, userid, rating) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE rating = ?");
$stmt->execute([$itemId, $userId, $rating, $rating]);

echo "Rating saved";
?>
