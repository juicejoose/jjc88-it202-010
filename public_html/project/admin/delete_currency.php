<?php
ob_start();
require(__DIR__ . "/../../../partials/nav.php");
<<<<<<< HEAD
<<<<<<< HEAD
//only admin has permission to delete
=======

// 05/09/2025 delete can also happen if its associated.
>>>>>>> 9e745c467f1e4602a960e32ec0b2df96d03a5b2a
if (!has_role("Admin") && !has_role("Moderator")) {
=======

if (!has_role("Admin")) {
>>>>>>> 3d7eba7341e63905aaee348b9d5d3c7865c61bb7
    flash("You don't have permission to do that", "danger");
    die(header("Location: " . get_url("home.php")));
}

$id = se($_GET, "id", -1, false);
if ($id > 0) {
    $db = getDB();
<<<<<<< HEAD
    $stmt = $db->prepare("DELETE FROM Currency WHERE id = :id");
<<<<<<< HEAD
    //jjc88 this will hard delete
=======
    //this will hard delete
>>>>>>> 3d7eba7341e63905aaee348b9d5d3c7865c61bb7
=======

>>>>>>> 9e745c467f1e4602a960e32ec0b2df96d03a5b2a
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
<<<<<<< HEAD
<<<<<<< HEAD
//redirect to list
=======
>>>>>>> 3d7eba7341e63905aaee348b9d5d3c7865c61bb7
=======

// Redirect to list page
>>>>>>> 9e745c467f1e4602a960e32ec0b2df96d03a5b2a
die(header("Location: " . get_url("admin/list_currency.php")));
ob_end_flush();
