<?php
require(__DIR__ . "/../../../partials/nav.php");
<<<<<<< HEAD
//only admin has permission to delete
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
    $stmt = $db->prepare("DELETE FROM Currency WHERE id = :id");
<<<<<<< HEAD
    //jjc88 this will hard delete
=======
    //this will hard delete
>>>>>>> 3d7eba7341e63905aaee348b9d5d3c7865c61bb7
    try {
        $stmt->execute([":id" => $id]);
        flash("Currency deleted", "success");
    } catch (PDOException $e) {
        error_log("Delete error: " . var_export($e, true));
        flash("Error deleting currency", "danger");
    }
}
<<<<<<< HEAD
//redirect to list
=======
>>>>>>> 3d7eba7341e63905aaee348b9d5d3c7865c61bb7
die(header("Location: " . get_url("admin/list_currency.php")));
