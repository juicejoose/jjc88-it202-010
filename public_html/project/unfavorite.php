<?php
ob_start();
require(__DIR__ . "/../../partials/nav.php");

if (!is_logged_in()) {
    flash("You must be logged in to modify favorites", "warning");
    die(header("Location: " . get_url("login.php")));
}

$user_id = get_user_id();
$currency_id = (int)se($_GET, "id", 0, false);

if ($currency_id > 0) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM `User Currency Favorites` WHERE user_id = :uid AND currency_id = :cid");
    try {
        $stmt->execute([":uid" => $user_id, ":cid" => $currency_id]);
        if ($stmt->rowCount() > 0) {
            flash("Removed from favorites", "success");
        } else {
            flash("Cannot unfavorite another user's favorite", "warning");
        }
    } catch (PDOException $e) {
        error_log("Unfavorite error: " . var_export($e, true));
        flash("Error removing favorite", "danger");
    }
}


$redirect = se($_SERVER, "HTTP_REFERER", get_url("currency.php"), false);
header("Location: " . $redirect);
exit();
ob_end_flush();