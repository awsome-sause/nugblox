<?php
require_once("../main/config.php");

if (!$isloggedin) {
    header("location: /");
    exit;
}

if ($user->authKey == null || $user->authKey == '') {
    $authKey = bin2hex(random_bytes(13));
    $authKey = substr($authKey, 0, 25);

    $stmt = $db->prepare("UPDATE users SET authKey=? WHERE id=?");
    $stmt->execute([$authKey, $user->id]);

    $user->authKey = $authKey;
}

$gameid = (int)$_GET['id'];

$a = $db->prepare("SELECT * FROM games WHERE id=?");
$a->execute([$gameid]);
$game = $a->fetch();

$visit = $db->prepare("INSERT INTO gamevisits (gameid, userid) VALUES (?, ?)");
$visit->execute([$gameid, $user->id]);

do {
    $port = rand(50001, 50500);
    $stmt = $db->prepare("SELECT COUNT(*) FROM games WHERE port=?");
    $stmt->execute([$port]);
    $count = $stmt->fetchColumn();
} while ($count > 0);

$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$isMobile = preg_match('/android|iphone|ipad|ipod|mobile|tablet/i', $userAgent);

$ip = 'infra.nuka.works';
$baseurl = 'https://nuglox.com/';
$authKey = $user->authKey;

if ($game['port'] == 0) {
    $db->prepare("UPDATE games SET port=? WHERE id=?")->execute([$port, $game['id']]);
    start_game($port, $game['id'], $game['authKey']);
    $portToUse = $port;
} else {
    $portToUse = $game['port'];
}

sleep(3);

if ($isMobile == 1) {
    header("location: nuglox://launch?authkey={$authKey}&ip={$ip}&port={$portToUse}&type=player&baseurl={$baseurl};gameid={$game['id']}");
    exit;
} else {
    header("location: NUGLOX:yes=1;authkey={$authKey};ip={$ip};port={$portToUse};type=player;baseurl={$baseurl};gameid={$game['id']}");
    exit;
}
