<?php
header('Content-Type: application/json');

require_once($_SERVER['DOCUMENT_ROOT'].'/main/config.php');

if (!$isloggedin) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$userId = $user->id;
$itemId = (int)($_POST['id'] ?? 0);

if (!$itemId) {
    echo json_encode(['success' => false, 'error' => 'Invalid item ID']);
    exit;
}

try {
    $db->beginTransaction();

    $stmt = $db->prepare("SELECT * FROM catalog WHERE id = ?");
    $stmt->execute([$itemId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        echo json_encode(['success' => false, 'error' => 'Item not found']);
        exit;
    }

    $itemType = strtolower($item['type']);

    if ($itemType === 'hat') {
        $stmt = $db->prepare("SELECT w.* FROM wearing w INNER JOIN catalog c ON w.itemid = c.id WHERE w.ownerid = ? AND LOWER(c.type) = 'hat' ORDER BY w.whenweared ASC");
        $stmt->execute([$userId]);
        $hats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($hats as $h) {
            if ((int)$h['itemid'] === $itemId) {
                $del = $db->prepare("DELETE FROM wearing WHERE id = ?");
                $del->execute([(int)$h['id']]);
                $db->commit();
                echo json_encode(['success' => true, 'action' => 'unwear']);
                exit;
            }
        }

        if (count($hats) >= 3) {
            $del = $db->prepare("DELETE FROM wearing WHERE id = ?");
            $del->execute([(int)$hats[0]['id']]);
        }

        $ins = $db->prepare("INSERT INTO wearing (ownerid, itemid, whenweared) VALUES (?, ?, NOW())");
        $ins->execute([$userId, $itemId]);
        $db->commit();
        echo json_encode(['success' => true, 'action' => count($hats) >= 3 ? 'swap' : 'wear']);
        exit;

    } else {
        $stmt = $db->prepare("SELECT w.* FROM wearing w INNER JOIN catalog c ON w.itemid = c.id WHERE w.ownerid = ? AND c.type = ?");
        $stmt->execute([$userId, $item['type']]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing && (int)$existing['itemid'] === $itemId) {
            $del = $db->prepare("DELETE FROM wearing WHERE id = ?");
            $del->execute([$existing['id']]);
            $db->commit();
            echo json_encode(['success' => true, 'action' => 'unwear']);
            exit;
        }

        if ($existing) {
            $del = $db->prepare("DELETE FROM wearing WHERE id = ?");
            $del->execute([$existing['id']]);
        }

        $ins = $db->prepare("INSERT INTO wearing (ownerid, itemid, whenweared) VALUES (?, ?, NOW())");
        $ins->execute([$userId, $itemId]);
        $db->commit();
        echo json_encode(['success' => true, 'action' => $existing ? 'swap' : 'wear']);
        exit;
    }

} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
