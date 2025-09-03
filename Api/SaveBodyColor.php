<?php
require_once($_SERVER['DOCUMENT_ROOT']."/main/config.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed, use POST']);
    exit;
}

if (!$isloggedin) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $user->id;

$headc = filter_input(INPUT_POST, 'headColor', FILTER_SANITIZE_STRING);
$torsoc = filter_input(INPUT_POST, 'torsoColor', FILTER_SANITIZE_STRING);
$leftarmc = filter_input(INPUT_POST, 'leftArmColor', FILTER_SANITIZE_STRING);
$rightarmc = filter_input(INPUT_POST, 'rightArmColor', FILTER_SANITIZE_STRING);
$leftlegc = filter_input(INPUT_POST, 'leftLegColor', FILTER_SANITIZE_STRING);
$rightlegc = filter_input(INPUT_POST, 'rightLegColor', FILTER_SANITIZE_STRING);
$bodytype = filter_input(INPUT_POST, 'bodyType', FILTER_SANITIZE_STRING);

function isValidHexColor($color) {
    return preg_match('/^#[0-9a-fA-F]{6}$/', $color);
}

$colors = compact('headc', 'torsoc', 'leftarmc', 'rightarmc', 'leftlegc', 'rightlegc');
foreach ($colors as $color) {
    if (!isValidHexColor($color)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid color format']);
        exit;
    }
}

try {
    $stmt = $db->prepare("UPDATE users SET 
        headc = ?, torsoc = ?, leftarmc = ?, rightarmc = ?, leftlegc = ?, rightlegc = ?, bodytype = ?
        WHERE id = ?");
    $stmt->execute([
        $headc,
        $torsoc,
        $leftarmc,
        $rightarmc,
        $leftlegc,
        $rightlegc,
        $bodytype,
        $userId
    ]);

    echo json_encode(['success' => true, 'message' => 'Colors and bodyType updated']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
}
?>
