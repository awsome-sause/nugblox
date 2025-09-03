<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/main/config.php');

if(!$isloggedin){
  header("location: /");
  exit;
}

$itemid = (int)$_POST['itemid'];

$stmt = $db->prepare("SELECT * FROM catalog WHERE id=?");
$stmt->execute([$itemid]);
if($stmt->rowCount() < 1){
  exit('Invalid item.');
}
$item = $stmt->fetch();

$price = (int)$item['price'];

if($user->bux < $price){
  exit('Not enough BUX.');
}

$x = $db->prepare("SELECT 1 FROM inventory WHERE itemid=? AND ownerid=? LIMIT 1");
$x->execute([$itemid, $user->id]);
if($x->rowCount() > 0){
  exit('Already owned.');
}

$b = $db->prepare("INSERT INTO inventory (itemid, ownerid) VALUES (?, ?)");
$b->execute([$itemid, $user->id]);

$u = $db->prepare("UPDATE users SET bux=bux-? WHERE id=?");
$u->execute([$price, $user->id]);

$u = $db->prepare("UPDATE users SET bux=bux+? WHERE id=?");
$u->execute([$price, $item['creatorid']]);

exit('Success');
?>
