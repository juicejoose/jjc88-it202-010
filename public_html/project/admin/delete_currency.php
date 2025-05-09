<?php
ob_start();
require(__DIR__ . "/../../../partials/nav.php");

// 05/09/2025 delete can also happen if its associated.
if (!has_role("Admin") && !has_role("Moderator")) {
    flash("You don't have permission to do that", "danger");
    die(header("Location: " . get_url("home.php")));
}

$id = se($_GET, "id", -1, false);
if ($id > 0) {
    $db = getDB();

    try {
        // Step 1: Delete associated records from User Currency Favorites
        $stmt = $db->prepare("DELETE FROM `User Currency Favorites` WHERE currency_id = :id");
        $stmt->execute([":id" => $id]);

        // Step 2: Delete the currency
        $stmt = $db->prepare("DELETE FROM Currency WHERE id = :id");
        $stmt->execute([":id" => $id]);

        flash("Currency deleted", "success");
    } catch (PDOException $e) {
        error_log("Delete error: " . var_export($e, true));
        flash("Error deleting currency", "danger");
    }
}

// Redirect to list page
die(header("Location: " . get_url("admin/list_currency.php")));
ob_end_flush();
