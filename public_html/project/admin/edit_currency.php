<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}

$id = se($_GET, "id", -1, false);

// Handle currency fetch and update
if (isset($_POST["base_currency"])) {
    foreach ($_POST as $k => $v) {
        if (!in_array($k, ["base_currency", "unit", "XAU", "XAG", "PA", "PL", "GBP", "EUR"])) {
            unset($_POST[$k]);
        }
        $quote = $_POST;
        error_log("Cleaned up POST: " . var_export($quote, true));
    }

    // Update data
    $db = getDB();
    $query = "UPDATE `Currency` SET ";

    $params = [];
    foreach ($quote as $k => $v) {
        if ($params) {
            $query .= ",";
        }
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
        flash("Updated record", "success");
    } catch (PDOException $e) {
        error_log("Something broke with the query" . var_export($e, true));
        flash("An error occurred", "danger");
    }
}

$currency = [];
if ($id > -1) {
    // fetch data
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

$form = [
    ["type" => "text", "id" => "base_currency", "name" => "base_currency", "label" => "Base Currency", "rules" => ["required" => true]],
    ["type" => "text", "id" => "unit", "name" => "unit", "label" => "Unit", "rules" => ["required" => true]],
    ["type" => "number", "id" => "XAU", "name" => "XAU", "label" => "XAU", "rules" => ["required" => true, "step"=>"any"]],
    ["type" => "number", "id" => "XAG", "name" => "XAG", "label" => "XAG", "rules" => ["required" => true, "step"=>"any"]],
    ["type" => "number", "id" => "PA", "name" => "PA", "label" => "PA", "rules" => ["required" => true, "step"=>"any"]],
    ["type" => "number", "id" => "PL", "name" => "PL", "label" => "PL", "rules" => ["required" => true, "step"=>"any"]],
    ["type" => "number", "id" => "GBP", "name" => "GBP", "label" => "GBP", "rules" => ["required" => true, "step"=>"any"]],
    ["type" => "number", "id" => "EUR", "name" => "EUR", "label" => "EUR", "rules" => ["required" => true, "step"=>"any"]],
];


foreach ($form as $k => $v) {
    if (isset($currency[$v["name"]])) {
        $form[$k]["value"] = $currency[$v["name"]];
    }
}
?>

<div class="container-fluid">
    <h3>Edit Currency</h3>
    <form method="POST" onsubmit="return validateCurrency(this)">
        <?php foreach ($form as $field): ?>
            <div class="mb-3">
                <?php render_input($field); ?>
            </div>
        <?php endforeach; ?>
        <?php render_button(["text" => "Update", "type" => "submit"]); ?>
    </form>
</div>
<script>
    ///jjc88 05/02/2025 JS Validations
    function validateCurrency(form) {
        let isValid = true;

        const baseCurrencyValue = form.base_currency.value.trim();
        if (!baseCurrencyValue || !/^[A-Za-z]{1,3}$/.test(baseCurrencyValue)) {
            console.log("Base Currency must be 1–3 letters");
            isValid = false;
        }

        const unitValue = form.unit.value.trim();
        if (!unitValue || !/^[A-Za-z]+$/.test(unitValue)) {
            console.log("Unit must contain only letters");
            isValid = false;
        }

        const fields = ["XAU", "XAG", "PA", "PL", "GBP", "EUR"];
        for (let i = 0; i < fields.length; i++) {
            let field = form[fields[i]];
            if (!field.value.trim() || isNaN(field.value) || parseFloat(field.value) <= 0) {
                console.log(`${fields[i]} must be a valid positive number`);
                isValid = false;
            }
        }

        return isValid;
    }
</script>

<?php
require_once(__DIR__ . "/../../../partials/flash.php");
?>
