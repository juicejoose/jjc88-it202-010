<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}
?>

<?php

//TODO handle currency fetch
if (isset($_POST["action"])) {
    $action = $_POST["action"];
    $currency = strtoupper(se($_POST, "currency", "", false)); // Get the currency symbol from the form input
    $quote = [];
    
    if (!empty($currency) && preg_match("/^[A-Z]{3}$/", $currency)) {
        if ($action === "fetch") {
            // Fetch the quote using your API function
            $result = fetch_quote($currency);

            // Log the raw API response to check the structure
            error_log("Raw Data from API: " . var_export($result, true));

            // Check if we received valid data from the API
            if (!empty($result)) {
                $quote = $result;
                $quote["is_api"] = 1; // Mark as API data

                // Log the transformed data
                error_log("Transformed Data for Database: " . var_export($quote, true));

                // Assuming 'Currency' table columns are as mentioned earlier:
                $db = getDB();
                $query = "INSERT INTO `Currency` ";
                $columns = [];
                $params = [];
                // Prepare the query based on the keys from the API response
                foreach ($quote as $k => $v) {
                    array_push($columns, "`$k`");
                    $params[":$k"] = $v;
                }
                $query .= "(" . join(",", $columns) . ")";
                $query .= "VALUES (" . join(",", array_keys($params)) . ")";

                try {
                    $stmt = $db->prepare($query);
                    $stmt->execute($params);
                    flash("Inserted record " . $db->lastInsertId(), "success");
                } catch (PDOException $e) {
                    error_log("Something broke with the query: " . var_export($e, true));
                    flash("An error occurred", "danger");
                }
            } else {
                flash("No data found for the provided currency symbol", "warning");
            }
        }
    } else {
        flash("You must provide a valid 3-letter currency symbol", "warning");
    }
}


//TODO handle manual create stock
?>
<div class="container-fluid">
    <h3>Create or Fetch Currency</h3>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link bg-success" href="#" onclick="switchTab('create')">Fetch</a>
        </li>
        <li class="nav-item">
            <a class="nav-link bg-success" href="#" onclick="switchTab('fetch')">Create</a>
        </li>
    </ul>
    <div id="fetch" class="tab-target">
        <form method="POST">
            <div>
                <label for="currency">Currency</label>
                <input type="search" name="currency" id="currency" placeholder="Currency" required>
            </div>
            <input type="hidden" name="action" value="fetch">
            <input type="submit" value="Fetch" class="btn btn-primary">
        </form>
    </div>
    <div id="create" style="display: none;" class="tab-target">
        <form method="POST">
            <div class="mb-3">
                <label for="base_currency">Base Currency</label>
                <input type="text" name="base_currency" id="base_currency" placeholder="Currency Ticker" required>
            </div>
            <div class="mb-3">
                <label for="unit">Unit</label>
                <input type="text" name="unit" id="unit" placeholder="Unit" required>
            </div>
            <div class="mb-3">
                <label for="low">XAU</label>
                <input type="number" name="XAU" id="XAU" placeholder="XAU" required>
            </div>
            <div class="mb-3">
                <label for="XAG">XAG</label>
                <input type="number" name="XAG" id="XAG" placeholder="XAG" required>
            </div>
            <div class="mb-3">
                <label for="PA">PA</label>
                <input type="number" name="PA" id="PA" placeholder="PA" required>
            </div>
            <div class="mb-3">
                <label for="PL">PL</label>
                <input type="number" name="PL" id="PL" placeholder="PL" required>
            </div>
            <div class="mb-3">
                <label for="GBP">GBP</label>
                <input type="number" name="GBP" id="GBP" placeholder="GBP" required>
            </div>
            <div class="mb-3">
                <label for="EUR">EUR</label>
                <input type="number" name="EUR" id="EUR" placeholder="EUR" required>
            </div>
            <div class="mb-3">
                <label for="latest_trading_day">Currency Date</label>
                <input type="date" name="latest_trading_day" id="latest_trading_day" placeholder="Currency Date" required>
            </div>
            <input type="hidden" name="action" value="create">
            <input type="submit" value="Create" class="btn btn-primary">
        </form>
    </div>
</div>
<script>
    function switchTab(tab) {
        let target = document.getElementById(tab);
        if (target) {
            let eles = document.getElementsByClassName("tab-target");
            for (let ele of eles) {
                ele.style.display = (ele.id === tab) ? "none" : "block";
            }
        }
    }
</script>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>