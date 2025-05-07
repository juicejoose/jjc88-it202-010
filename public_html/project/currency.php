<?php
require(__DIR__ . "/../../partials/nav.php");

// Allowed columns and sort directions
$allowed_columns = ["base_currency", "unit", "created", "modified"];
$sort_directions = ["asc", "desc"];

$search = "";
$created_date = "";
$column = se($_POST, "column", "created", false); // default sort column
$order = se($_POST, "order", "desc", false);      // default sort direction
$limit = se($_POST, "limit", 10, false);          // default limit

// Validate column and order
if (!in_array($column, $allowed_columns)) {
    $column = "created";
}
if (!in_array($order, $sort_directions)) {
    $order = "desc";
}
if (!is_numeric($limit) || $limit < 1 || $limit > 100) {
    $limit = 10;
}

// Build base query
$query = "SELECT id, base_currency, unit, XAU, XAG, PA, PL, GBP, EUR, created, modified, is_api 
          FROM `Currency`";
$params = [];
$conditions = [];

// Search filter
if (isset($_POST["search"])) {
    $search = se($_POST, "search", "", false);
    if (!empty($search)) {
        $conditions[] = "base_currency LIKE :search";
        $params[":search"] = "%$search%";
    }
}

// Created date filter
if (isset($_POST["created_date"])) {
    $created_date = se($_POST, "created_date", "", false);
    if (!empty($created_date)) {
        $conditions[] = "DATE(created) = :created_date";
        $params[":created_date"] = $created_date;
    }
}

// Apply conditions
if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

// Add ORDER BY and LIMIT
$query .= " ORDER BY $column $order LIMIT :limit";
$params[":limit"] = (int)$limit;

$db = getDB();
$stmt = $db->prepare($query);
$results = [];

foreach ($params as $key => $val) {
    $type = is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR;
    $stmt->bindValue($key, $val, $type);
}

try {
    $stmt->execute();
    $r = $stmt->fetchAll();
    if ($r) {
        $results = array_map(function ($row) {
            return [
                "id" => $row["id"],
                "base_currency" => $row["base_currency"],
                "unit" => $row["unit"],
                "XAU" => $row["XAU"],
                "created" => $row["created"],
                "modified" => $row["modified"],
                "is_api" => $row["is_api"]
            ];
        }, $r);
    } else {
        flash("No matches found", "warning");
    }
} catch (PDOException $e) {
    error_log("Error fetching currencies " . var_export($e, true));
    flash("Unhandled error occurred", "danger");
}

// Count total results (ignoring LIMIT)
$count_query = "SELECT COUNT(*) as total FROM `Currency`";
$count_params = $params; // Use the same parameters
if (!empty($conditions)) {
    $count_query .= " WHERE " . implode(" AND ", $conditions);
}

$count_stmt = $db->prepare($count_query);
foreach ($count_params as $key => $val) {
    $type = is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR;
    $count_stmt->bindValue($key, $val, $type);
}

$total_results = 0;
try {
    $count_stmt->execute();
    $count_result = $count_stmt->fetch();
    if ($count_result) {
        $total_results = (int)$count_result["total"];
    }
} catch (PDOException $e) {
    error_log("Error fetching total count: " . var_export($e, true));
}

// Table setup
$table = [
    "data" => $results,
    "view_url" => get_url("entry.php"),
    "favorite_url" => get_url("favorite.php"),
    "classes" => "btn btn-secondary",
    "columns" => [
        "base_currency" => "Base Currency",
        "unit" => "Unit",
        "XAU" => "XAU",
        "created" => "Created Date",
        "modified" => "Modified Date",
        "is_api" => "From API"
    ]
];

// Build select dropdown options
$column_options = array_map(fn($c) => [$c => ucfirst($c)], $allowed_columns);
$order_options = array_map(fn($o) => [$o => strtoupper($o)], $sort_directions);
?>

<div class="container-fluid">
    <h3>List Currencies</h3>
    <form method="POST" class="mb-3 row g-2">
        <div class="col-md-2">
            <?php render_input(["type" => "search", "name" => "search", "label" => "Base Currency", "placeholder" => "Search Base Currency", "value" => $search]); ?>
        </div>
        <div class="col-md-2">
            <?php render_input(["type" => "date", "name" => "created_date", "label" => "Created Date", "value" => $created_date]); ?>
        </div>
        <div class="col-md-2">
            <?php render_input([
                "type" => "select",
                "name" => "column",
                "label" => "Sort Column",
                "options" => $column_options,
                "value" => $column
            ]); ?>
        </div>
        <div class="col-md-2">
            <?php render_input([
                "type" => "select",
                "name" => "order",
                "label" => "Order",
                "options" => $order_options,
                "value" => $order
            ]); ?>
        </div>
        <div class="col-md-2">
            <?php render_input(["type" => "number", "name" => "limit", "label" => "Limit", "value" => $limit, "rules" => ["min" => 1, "max" => 100]]); ?>
        </div>
        <div class="col-md-2 align-self-end">
            <?php render_button(["text" => "Search", "type" => "submit", "classes" => "btn btn-primary"]); ?>
        </div>
    </form>
<!-- Display total number of results -->
<div class="mb-3">
        <?php
        $on_page = count($results);
        echo "Showing $on_page results";
        ?>
    </div>

    <?php render_table($table); ?>
</div>

<?php require_once(__DIR__ . "/../../partials/flash.php"); ?>
