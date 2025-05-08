<?php
require(__DIR__ . "/../../partials/nav.php");

$user_id = get_user_id();  // Retrieve the logged-in user's ID

// Define allowed columns and sort directions
$allowed_columns = ["base_currency", "unit", "created", "modified"];
$sort_directions = ["asc", "desc"];

// Capture POST variables
$search = "";
$column = se($_POST, "column", "created", false);  // Default column to sort by "created"
$order = se($_POST, "order", "desc", false);  // Default order to "desc"
$limit = se($_POST, "limit", 10, false);  // Default limit to 10
$page = (int)se($_POST, "page", 1, false);  // Default page to 1
$created_date = se($_POST, "created_date", "", false);  // Filter by created date

// Validate column and order
if (!in_array($column, $allowed_columns)) {
    $column = "created";  // Fallback to "created" if invalid column
}
if (!in_array($order, $sort_directions)) {
    $order = "desc";  // Fallback to "desc" if invalid order
}
if (!is_numeric($limit) || $limit < 1 || $limit > 100) {
    $limit = 10;  // Ensure limit is between 1 and 100
}

$offset = ($page - 1) * $limit;  // Calculate offset for pagination

// Build query to select currencies that are NOT favorited by the user
$query = "SELECT c.id, c.base_currency, c.unit, c.XAU, c.XAG, c.PA, c.PL, c.GBP, c.EUR, c.created, c.modified, c.is_api
          FROM `Currency` c 
          LEFT JOIN `User Currency Favorites` uf ON c.id = uf.currency_id AND uf.user_id = :user_id
          WHERE uf.currency_id IS NULL";  // This ensures that the currency is not favorited by the user

$params = [":user_id" => $user_id];  // Parameter for user_id

// Add search filter if search term is provided
if (isset($_POST["search"])) {
    $search = se($_POST, "search", "", false);
    if (!empty($search)) {
        $query .= " AND c.base_currency LIKE :search";
        $params[":search"] = "%$search%";
    }
}

// Filter by created date if provided
if (!empty($created_date)) {
    $query .= " AND DATE(c.created) = :created_date";
    $params[":created_date"] = $created_date;
}

// Build total query for pagination
$total_query = "SELECT COUNT(*) as total 
                FROM `Currency` c 
                LEFT JOIN `User Currency Favorites` uf ON c.id = uf.currency_id AND uf.user_id = :user_id
                WHERE uf.currency_id IS NULL" . 
                (isset($params[":search"]) ? " AND c.base_currency LIKE :search" : "") . 
                (isset($params[":created_date"]) ? " AND DATE(c.created) = :created_date" : "");

// Prepare total query
$total_stmt = getDB()->prepare($total_query);
foreach ($params as $key => $val) {
    $type = is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR;
    $total_stmt->bindValue($key, $val, $type);
}
$total_stmt->execute();
$total = $total_stmt->fetch()["total"] ?? 0;  // Get total count

// Add sorting and pagination to the main query
$query .= " ORDER BY c.$column $order LIMIT :limit OFFSET :offset";
$params[":limit"] = (int)$limit;
$params[":offset"] = (int)$offset;

// Prepare and execute the main query
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

// Build table data for rendering
$table = [
    "data" => $results,
    "view_url" => get_url("entry.php"),
    "favorite_url" => get_url("favorite.php"),  // Change URL to favorite.php to allow the user to favorite a currency
    "classes" => "table table-striped",
    "view_label" => "View",
    "view_classes" => "btn btn-primary",
    "favorite_label" => "Favorite",
    "favorite_classes" => "btn btn-success",
    "columns" => [
        "base_currency" => "Base Currency",
        "unit" => "Unit",
        "XAU" => "XAU",
        "created" => "Created Date",
        "modified" => "Modified Date",
        "is_api" => "From API"
    ]
];
?>

<div class="container-fluid">
    <h3>Available Currencies (Not Favorited)</h3>
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
                "options" => array_map(fn($c) => [$c => ucfirst($c)], $allowed_columns),
                "value" => $column
            ]); ?>
        </div>
        <div class="col-md-2">
            <?php render_input([
                "type" => "select",
                "name" => "order",
                "label" => "Order",
                "options" => array_map(fn($o) => [$o => strtoupper($o)], $sort_directions),
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

    <div class="mb-3">
        <?php
        $on_page = count($results);
        echo "Showing $on_page of $total results";
        ?>
    </div>

    <?php render_table($table); ?>
</div>

<?php require_once(__DIR__ . "/../../partials/flash.php"); ?>
