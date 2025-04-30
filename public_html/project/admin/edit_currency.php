<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}
?>

<?php
$id = se($_GET, "id", -1, false);
//TODO handle stock fetch
if (isset($_POST["base_currency"])) {
    foreach ($_POST as $k => $v) {
        if (!in_array($k, ["base_currency", "unit", "XAU", "XAG", "PA", "PL", "GBP", "EUR"])) {
            unset($_POST[$k]);
        }
        $quote = $_POST;
        error_log("Cleaned up POST: " . var_export($quote, true));
    }
    // Ideally only the table name should need to change for most queries
    //update data
    $db = getDB();
    $query = "UPDATE `Currency` SET ";

    $params = [];
    //per record
    foreach ($quote as $k => $v) {

        if ($params) {
            $query .= ",";
        }
        //be sure $k is trusted as this is a source of sql injection
        $query .= "$k=:$k";
        $params[":$k"] = $v;
    }

    $query .= " WHERE id = :id";
    $params[":id"] = $id;
    error_log("Query: " . $query);
    error_log("Params: " . var_export($params, true));
    try {
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        flash("Updated record ", "success");
    } catch (PDOException $e) {
        error_log("Something broke with the query" . var_export($e, true));
        flash("An error occurred", "danger");
    }
}

$currency = [];
if ($id > -1) {
    //fetch
    $db = getDB();
    $query = "SELECT base_currency, unit, XAU, XAG, PA, PL, GBP, EUR FROM `Currency` WHERE id = :id";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":id" => $id]);
        $r = $stmt->fetch();
        if ($r) {
            $currency = $r;
        }
    } catch (PDOException $e) {
        error_log("Error fetching record: " . var_export($e, true));
        flash("Error fetching record", "danger");
    }
} else {
    flash("Invalid id passed", "danger");
    die(header("Location:" . get_url("admin/list_currency.php")));
}

?>
<div class="container-fluid">
    <h3>Edit Currency</h3>
    <form method="POST">
        <div class="mb-3">
            <label for="base_currency">Base Currency</label>
            <input type="text" name="base_currency" id="base_currency" placeholder="Base Currency" required value="<?php se($currency, "base_currency"); ?>">
        </div>
        <div class="mb-3">
            <label for="unit">Unit</label>
            <input type="text" name="unit" id="unit" placeholder="Unit" required value="<?php se($currency, "unit"); ?>">
        </div>
        <div class="mb-3">
            <label for="XAU">XAU</label>
            <input type="number" name="XAU" id="XAU" placeholder="XAU" required value="<?php se($currency, "XAU"); ?>">
        </div>
        <div class="mb-3">
            <label for="XAG">XAG</label>
            <input type="number" name="XAG" id="XAG" placeholder="XAG" required value="<?php se($currency, "XAG"); ?>">
        </div>
        <div class="mb-3">
            <label for="PA">PA</label>
            <input type="number" name="PA" id="PA" placeholder="PA" required value="<?php se($currency, "PA"); ?>">
        </div>
        <div class="mb-3">
            <label for="PL">PL</label>
            <input type="number" name="PL" id="PL" placeholder="PL" required value="<?php se($currency, "PL"); ?>">
        </div>
        <div class="mb-3">
            <label for="GBP">GBP</label>
            <input type="number" name="GBP" id="GBP" placeholder="GBP" required value="<?php se($currency, "GBP"); ?>">
        </div>
        <div class="mb-3">
            <label for="EUR">EUR</label>
            <input type="number" name="EUR" id="EUR" placeholder="EUR" required value="<?php se($currency, "EUR"); ?>">
        </div>
        <input type="submit" value="Update" class="btn btn-primary">
    </form>

</div>


<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>