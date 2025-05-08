<?php
require(__DIR__ . "/../../../partials/nav.php");
//only admin has permission to delete
if (!has_role("Admin") && !has_role("Moderator")) {
    flash("You don't have permission to do that", "danger");
    die(header("Location: " . get_url("home.php")));
}

$id = se($_GET, "id", -1, false);
if ($id > 0) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM Currency WHERE id = :id");
    //jjc88 this will hard delete
    try {
        $stmt->execute([":id" => $id]);
        flash("Currency deleted", "success");
    } catch (PDOException $e) {
        error_log("Delete error: " . var_export($e, true));
        flash("Error deleting currency", "danger");
    }
}
//redirect to list
die(header("Location: " . get_url("admin/list_currency.php")));
