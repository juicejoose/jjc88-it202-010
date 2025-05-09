<?php
require(__DIR__ . "/../../../partials/nav.php");

<<<<<<< HEAD
if (!has_role("Admin") && !has_role("Moderator")) {
=======
if (!has_role("Admin")) {
>>>>>>> 3d7eba7341e63905aaee348b9d5d3c7865c61bb7
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}

// Reusable form definition
$form = [
    ["type" => "text", "id" => "base_currency", "name" => "base_currency", "label" => "Base Currency", "rules" => ["required" => true]],
    ["type" => "text", "id" => "unit", "name" => "unit", "label" => "Unit", "rules" => ["required" => true]],
    ["type" => "number", "id" => "XAU", "name" => "XAU", "label" => "XAU", "rules" => ["required" => true]],
    ["type" => "number", "id" => "XAG", "name" => "XAG", "label" => "XAG", "rules" => ["required" => true]],
    ["type" => "number", "id" => "PA", "name" => "PA", "label" => "PA", "rules" => ["required" => true]],
    ["type" => "number", "id" => "PL", "name" => "PL", "label" => "PL", "rules" => ["required" => true]],
    ["type" => "number", "id" => "GBP", "name" => "GBP", "label" => "GBP", "rules" => ["required" => true]],
    ["type" => "number", "id" => "EUR", "name" => "EUR", "label" => "EUR", "rules" => ["required" => true]],
];

// Handle form actions
if (isset($_POST["action"])) {
    $action = $_POST["action"];

    if ($action === "fetch") {
        $currency = strtoupper(se($_POST, "currency", "", false));
        if (!empty($currency) && preg_match("/^[A-Z]{3}$/", $currency)) {
            $result = fetch_quote($currency);
            error_log("Raw API data: " . var_export($result, true));

            if (!empty($result)) {
                $result["is_api"] = 1;
                $db = getDB();
                $columns = array_keys($result);
                $query = "INSERT INTO `Currency` (`" . implode("`,`", $columns) . "`) VALUES (:" . implode(",:", $columns) . ")";
                $params = array_combine(array_map(fn($k) => ":$k", $columns), array_values($result));

                try {
                    $stmt = $db->prepare($query);
                    $stmt->execute($params);
                    flash("Inserted record " . $db->lastInsertId(), "success");
                } catch (PDOException $e) {
                    error_log("Insert error: " . var_export($e, true));
                    flash("Insert failed", "danger");
                }
            } else {
                flash("No data found for $currency", "warning");
            }
        }
    } elseif ($action === "create") {
        $data = [];
        foreach ($form as $field) {
            $data[$field["name"]] = se($_POST, $field["name"], "", false);
        }
        $data["is_api"] = 0;

        if (!empty($data["base_currency"]) && !empty($data["unit"])) {
            $db = getDB();
            $query = "INSERT INTO `Currency` (`base_currency`, `unit`, `XAU`, `XAG`, `PA`, `PL`, `GBP`, `EUR`, `is_api`) 
                      VALUES (:base_currency, :unit, :XAU, :XAG, :PA, :PL, :GBP, :EUR, :is_api)";

            try {
                $stmt = $db->prepare($query);
                $stmt->execute($data);
                flash("Inserted record " . $db->lastInsertId(), "success");
            } catch (PDOException $e) {
                error_log("Create error: " . var_export($e, true));
                flash("Create failed", "danger");
            }
        } else {
            flash("Base currency and unit are required", "warning");
        }
    }
}
?>

<div class="container-fluid">
    <h3>Create or Fetch Currency</h3>
    <ul class="nav nav-tabs">
        <li class="nav-item"><a class="nav-link bg-warning" href="#" onclick="switchTab('fetch')">Fetch</a></li>
        <li class="nav-item"><a class="nav-link bg-warning" href="#" onclick="switchTab('create')">Create</a></li>
    </ul>

    <div id="fetch" class="tab-target">
        <form method="POST">
            <div class="mb-3">
                <?php render_input([
                    "type" => "text",
                    "name" => "currency",
                    "id" => "currency",
                    "label" => "Currency Symbol",
                    "rules" => ["required" => true]
                ]); ?>
            </div>
            <input type="hidden" name="action" value="fetch">
            <button type="submit" class="btn btn-primary">Fetch</button>
        </form>
    </div>

    <div id="create" class="tab-target" style="display:none;">
        <form method="POST" onsubmit="return validateCurrency(this)">
            <?php foreach ($form as $field): ?>
                <div class="mb-3"><?php render_input($field); ?></div>
            <?php endforeach; ?>
            <input type="hidden" name="action" value="create">
            <button type="submit" class="btn btn-primary">Create</button>
        </form>
    </div>
</div>

<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-target').forEach(el => {
            el.style.display = (el.id === tabId) ? 'block' : 'none';
        });
    }
    // Default to fetch tab
    switchTab('fetch');
    //jjc88 05/02/2025 js validations 
    function validateCurrency(form) {
    
    let isValid = true;

    const baseCurrencyValue = form.base_currency.value.trim();
    if (!baseCurrencyValue || !/^[A-Za-z]{1,3}$/.test(baseCurrencyValue)) {
        console.log("Base Currency must be 1-3 letters");
        isValid = false;
    }

    // Validate only letters
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

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>
