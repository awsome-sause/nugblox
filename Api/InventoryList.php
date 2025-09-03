<?php
include 'main/nav.php';

if (!$isloggedin) {
    http_response_code(403);
    exit;
}

$request = $_GET['request'] ?? '';

function getWearingAndInventory($db, $userId) {
    $wearing = $db->prepare("
        SELECT c.id, c.name 
        FROM catalog c 
        INNER JOIN wearing w ON w.itemid = c.id 
        WHERE w.ownerid = ? 
        ORDER BY w.whenweared DESC
    ");
    $wearing->execute([$userId]);
    $wearingItems = $wearing->fetchAll(PDO::FETCH_ASSOC);

    $inventory = $db->prepare("
        SELECT c.id, c.name 
        FROM catalog c 
        INNER JOIN inventory i ON i.itemid = c.id 
        WHERE i.ownerid = ? 
        ORDER BY i.whenbought DESC
    ");
    $inventory->execute([$userId]);
    $inventoryItems = $inventory->fetchAll(PDO::FETCH_ASSOC);

    return ['wearing' => $wearingItems, 'inventory' => $inventoryItems];
}

$wearingAndInventory = getWearingAndInventory($db, $user->id);
$wearingItems = $wearingAndInventory['wearing'];
$inventoryItems = $wearingAndInventory['inventory'];

if ($request === 'wardrobe' || $request === 'cr') {
    ?>
    <h3 style="font-size: 16px; color: #003366; margin-bottom: 6px;">Currently Wearing</h3>
    <div id="wearingList" class="item-list">
        <?php foreach ($wearingItems as $item): ?>
            <div class="item-container" data-item-id="<?= $item['id'] ?>">
                <div class="item-box" style="background-image: url('/rendering/CatalogThumbnail.php?id=<?= $item['id'] ?>');"></div>
                <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                <button class="unwear-button" onclick="toggleWear(<?= $item['id'] ?>)">Unwear</button>
            </div>
        <?php endforeach; ?>
    </div>

    <br>
    <h3 style="font-size: 16px; color: #003366; margin-bottom: 6px;">My Inventory</h3>
    <div id="inventoryList" class="item-list">
        <?php foreach ($inventoryItems as $item): ?>
            <div class="item-container" data-item-id="<?= $item['id'] ?>">
                <div class="item-box" style="background-image: url('/rendering/CatalogThumbnail.php?id=<?= $item['id'] ?>');"></div>
                <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                <button class="wear-button" onclick="toggleWear(<?= $item['id'] ?>)">Wear</button>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
} else {
    http_response_code(400);
}
