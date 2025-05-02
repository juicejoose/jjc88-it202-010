<?php
require(__DIR__ . "/../../partials/nav.php");

if (!is_logged_in()) {
    flash("You must be logged in to view this page", "warning");
    die(header("Location: " . get_url("login.php")));
}

$id = se($_GET, "id", -1, false);

$currency = [];
if ($id > -1) {
    $db = getDB();
    $query = "SELECT base_currency, unit, XAU, XAG, PA, PL, GBP, EUR, created, modified FROM `Currency` WHERE id = :id";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":id" => $id]);
        $r = $stmt->fetch();
        if ($r) {
            $currency = $r;
        } else {
            flash("Currency not found", "warning");
        }
    } catch (PDOException $e) {
        error_log("Error fetching record: " . var_export($e, true));
        flash("Error fetching record", "danger");
    }
} else {
    flash("Invalid ID passed", "danger");
    die(header("Location: " . get_url("home.php")));
}
?>

<div class="container-fluid">
    <h3>View Currency</h3>
    <?php if (!empty($currency)): ?>
        <div class="card p-3">
            <?php foreach ($currency as $label => $value): ?>
                <div class="mb-2">
                    <strong><?= htmlspecialchars(ucfirst($label)) ?>:</strong>
                    <span><?= htmlspecialchars($value) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No currency details available.</p>
    <?php endif; ?>
</div>

<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>
