<?php
require(__DIR__ . "/../../partials/nav.php");

$allowed_columns = ["base_currency", "unit", "created", "modified"];
$sort_directions = ["asc", "desc"];

$search = "";
$column = se($_POST, "column", "created", false); // Default to sorting by created date
$order = se($_POST, "order", "desc", false);      // Default sort direction to descending
$limit = se($_POST, "limit", 10, false);          // Default limit for the number of results per page
$page = (int)se($_POST, "page", 1, false);        // Current page number (defaults to 1)
$created_date = se($_POST, "created_date", "", false); // New field for created date

if (!in_array($column, $allowed_columns)) {
    $column = "created"; // Ensure column is valid, default to 'created'
}
if (!in_array($order, $sort_directions)) {
    $order = "desc"; // Ensure sort direction is valid, default to 'desc'
}
if (!is_numeric($limit) || $limit < 1 || $limit > 100) {
    $limit = 10; // Default limit is 25, ensuring it's between 1 and 100
}
if ($page < 1) {
    $page = 1; // Ensure valid page number
}

$offset = ($page - 1) * $limit; // Calculate offset for pagination

$query = "SELECT 
            c.id, 
            c.base_currency, 
            c.unit, 
            c.XAU, 
            c.XAG, 
            c.PA, 
            c.PL, 
            c.GBP, 
            c.EUR, 
            c.created, 
            c.modified, 
            c.is_api,
            u.username
          FROM `User Currency Favorites` uf
          JOIN `Currency` c ON uf.currency_id = c.id
          JOIN Users u ON uf.user_id = u.id";

$params = [];
$conditions = [];

// Search filter
if (isset($_POST["search"])) {
    $search = se($_POST, "search", "", false);
    if (!empty($search)) {
        $conditions[] = "(c.base_currency LIKE :search OR u.username LIKE :search)";
        $params[":search"] = "%$search%";
    }
}

// Filter by created date if a date is selected
if (!empty($created_date)) {
    $conditions[] = "DATE(c.created) = :created_date"; // Filter by created date
    $params[":created_date"] = $created_date; // Add date filter parameter
}

// Add conditions to the query if there are any
if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

// Apply sorting and pagination
$query .= " ORDER BY $column $order LIMIT :limit OFFSET :offset";
$params[":limit"] = (int)$limit;
$params[":offset"] = (int)$offset;

$db = getDB();
$stmt = $db->prepare($query);
$results = [];

// Bind the parameters for the query
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
                "is_api" => $row["is_api"],
                "username" => $row["username"]
            ];
        }, $r);
    } else {
        flash("No matches found", "warning");
    }
} catch (PDOException $e) {
    error_log("Error fetching currencies " . var_export($e, true));
    flash("Unhandled error occurred", "danger");
}

// Count total results for pagination
$count_query = "SELECT COUNT(*) as total FROM `User Currency Favorites` uf
                JOIN `Currency` c ON uf.currency_id = c.id
                JOIN Users u ON uf.user_id = u.id";
if (!empty($conditions)) {
    $count_query .= " WHERE " . implode(" AND ", $conditions);
}

$count_stmt = $db->prepare($count_query);
foreach ($params as $key => $val) {
    if ($key !== ":limit" && $key !== ":offset") {
        $type = is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR;
        $count_stmt->bindValue($key, $val, $type);
    }
}

$total_results = 0;
try {
    $count_stmt->execute();
    $count_result = $count_stmt->fetch();
    if ($count_result) {
        $total_results = (int)$count_result["total"];
    }
} catch (PDOException $e) {
    error_log("Error fetching count: " . var_export($e, true));
}

$total_pages = ceil($total_results / $limit); // Calculate total pages for pagination

$table = [
    "data" => $results,
    "view_url" => get_url("entry.php"),
    "favorite_url" => get_url("unfavorite.php"),
    "classes" => "table table-striped",
    "view_label" => "View",
    "view_classes" => "btn btn-primary",
    "favorite_label" => "Unfavorite",
    "favorite_classes" => "btn btn-warning",
    "columns" => [
        "base_currency" => "Base Currency",
        "unit" => "Unit",
        "XAU" => "XAU",
        "XAG" => "XAG",
        "username" => "Added By",
        "created" => "Created Date"  // Add Created Date to the columns
    ],
    "current_user_id" => get_user_id()
];

// Add `created` to the sort column dropdown options
$column_options = array_map(fn($c) => [$c => ucfirst($c)], $allowed_columns);
$order_options = array_map(fn($o) => [$o => strtoupper($o)], $sort_directions);
?>

<div class="container-fluid">
    <h3>All User Favorite Currencies</h3>
    <form method="POST" class="mb-3 row g-2">
        <div class="col-md-2">
            <?php render_input(["type" => "search", "name" => "search", "label" => "Search", "placeholder" => "Base Currency or Username", "value" => $search]); ?>
        </div>
        <div class="col-md-2">
            <?php render_input(["type" => "date", "name" => "created_date", "label" => "Created Date", "value" => $created_date]); ?>
        </div>
        <div class="col-md-2">
            <?php render_input(["type" => "select", "name" => "column", "label" => "Sort Column", "options" => $column_options, "value" => $column]); ?>
        </div>
        <div class="col-md-2">
            <?php render_input(["type" => "select", "name" => "order", "label" => "Order", "options" => $order_options, "value" => $order]); ?>
        </div>
        <div class="col-md-2">
            <?php render_input(["type" => "number", "name" => "limit", "label" => "Limit", "value" => $limit, "rules" => ["min" => 1, "max" => 100]]); ?>
        </div>
        <div class="col-md-2 align-self-end">
            <?php render_button(["text" => "Search", "type" => "submit", "classes" => "btn btn-primary"]); ?>
        </div>
    </form>

    <div class="mb-2">
        Showing <?= count($results) ?> of <?= $total_results ?> results
    </div>

    <?php render_table($table); ?>

    <form method="POST" class="d-flex gap-2 align-items-center mt-3">
        <input type="hidden" name="search" value="<?= $search ?>">
        <input type="hidden" name="column" value="<?= $column ?>">
        <input type="hidden" name="order" value="<?= $order ?>">
        <input type="hidden" name="limit" value="<?= $limit ?>">

        <?php if ($page > 1): ?>
            <button class="btn btn-secondary" name="page" value="<?= $page - 1 ?>">Previous</button>
        <?php endif; ?>

        <?php if ($page < $total_pages): ?>
            <button class="btn btn-secondary" name="page" value="<?= $page + 1 ?>">Next</button>
        <?php endif; ?>
    </form>
</div>

<?php require(__DIR__ . "/../../partials/flash.php"); ?>
