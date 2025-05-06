<?php
require(__DIR__ . "/../../partials/nav.php");

// Get the current user ID
$user_id = get_user_id();

if (!$user_id) {
    flash("You must be logged in to favorite a currency", "warning");
    die(header("Location: " . get_url("login.php")));
}

$currency_id = (int)se($_GET, "id", 0, false);
$db = getDB();

// Check if the currency is already favorited
$stmt = $db->prepare("SELECT id FROM `User Currency Favorites` WHERE user_id = :uid AND currency_id = :cid");
$stmt->execute([":uid" => $user_id, ":cid" => $currency_id]);
$exists = $stmt->fetch();

if ($exists) {
    flash("Currency is already in your favorites", "info");
    die(header("Location: " . get_url("currency.php"))); // Adjust if needed
}

// Get currency details from Currency table
$stmt = $db->prepare("SELECT * FROM `Currency` WHERE id = :cid");
$stmt->execute([":cid" => $currency_id]);
$currency = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$currency) {
    flash("Currency not found", "danger");
    die(header("Location: " . get_url("currency.php")));
}

// Insert into favorites
$query = "INSERT INTO `User Currency Favorites` 
    (user_id, currency_id, base_currency, unit, XAU, XAG, PA, PL, GBP, EUR, is_api) 
    VALUES 
    (:uid, :cid, :base_currency, :unit, :XAU, :XAG, :PA, :PL, :GBP, :EUR, :is_api)";

$params = [
    ":uid" => $user_id,
    ":cid" => $currency["id"],
    ":base_currency" => $currency["base_currency"],
    ":unit" => $currency["unit"],
    ":XAU" => $currency["XAU"],
    ":XAG" => $currency["XAG"],
    ":PA" => $currency["PA"],
    ":PL" => $currency["PL"],
    ":GBP" => $currency["GBP"],
    ":EUR" => $currency["EUR"],
    ":is_api" => $currency["is_api"]
];

try {
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    flash("Currency added to favorites!", "success");
} catch (PDOException $e) {
    error_log("Favorite Insert Error: " . var_export($e, true));
    flash("Error adding favorite", "danger");
}

die(header("Location: " . get_url("currency.php")));
?>
