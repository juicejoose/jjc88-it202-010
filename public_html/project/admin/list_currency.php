<?php
require(__DIR__ . "/../../../partials/nav.php");

<<<<<<< HEAD
if (!has_role("Admin") && !has_role("Moderator")) {
=======
if (!has_role("Admin")) {
>>>>>>> 3d7eba7341e63905aaee348b9d5d3c7865c61bb7
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("home.php")));
}

<<<<<<< HEAD
// Default sorting values
$allowed_columns = ["base_currency", "unit", "created", "modified"];
$sort_directions = ["asc", "desc"];
<<<<<<< HEAD
$column = se($_POST, "column", "created", false);  // Default sort by 'created'
$order = se($_POST, "order", "desc", false);      // Default sort order is descending
$limit = se($_POST, "limit", 10, false);          // Default limit is 10
=======
// Handle search input
//jjc88  05/1/2025 search and filter logic to show user their specificed search
>>>>>>> 3d7eba7341e63905aaee348b9d5d3c7865c61bb7
=======
$column = se($_POST, "column", "created", false);
$order = se($_POST, "order", "desc", false);      
$limit = se($_POST, "limit", 10, false);          
>>>>>>> 9e745c467f1e4602a960e32ec0b2df96d03a5b2a
$search = "";
$created_date = "";
$query = "SELECT id, base_currency, unit, XAU, XAG, PA, PL, GBP, EUR, created, modified, is_api 
          FROM `Currency`";
$params = [];

<<<<<<< HEAD
// Handle search input
=======
>>>>>>> 3d7eba7341e63905aaee348b9d5d3c7865c61bb7
if (isset($_POST["search"])) {
    $search = se($_POST, "search", "", false);
    if (!empty($search)) {
        $query .= " WHERE base_currency LIKE :search";
        $params[":search"] = "%$search%";
    }
}
<<<<<<< HEAD

// Handle created_date filter
=======
>>>>>>> 3d7eba7341e63905aaee348b9d5d3c7865c61bb7
if (isset($_POST["created_date"])) {
    $created_date = se($_POST, "created_date", "", false);
    if (!empty($created_date)) {
        if (empty($params)) {
            $query .= " WHERE DATE(created) = :created_date";
        } else {
            $query .= " AND DATE(created) = :created_date";
        }
        $params[":created_date"] = $created_date;
    }
}

<<<<<<< HEAD
// Add sorting, ordering, and limit
if (!in_array($column, $allowed_columns)) {
    $column = "created";  // Default column for sorting
}
if (!in_array($order, $sort_directions)) {
    $order = "desc";  // Default sort order
}

// Directly add the limit value into the query string
$query .= " ORDER BY $column $order LIMIT $limit";
=======


$query .= " ORDER BY created DESC LIMIT 25";
>>>>>>> 3d7eba7341e63905aaee348b9d5d3c7865c61bb7

$db = getDB();
$stmt = $db->prepare($query);
$results = [];

try {
    $stmt->execute($params);
    $r = $stmt->fetchAll();
    if ($r) {
        $results = $r;
    } else {
        flash("No matches found", "warning");
    }
} catch (PDOException $e) {
    error_log("Error fetching currencies " . var_export($e, true));
    flash("Unhandled error occurred", "danger");
}

$table = [
    "data" => $results,
    "view_url" => get_url("entry.php"),
    "edit_url" => get_url("admin/edit_currency.php"),
    "delete_url" => get_url("admin/delete_currency.php"),
<<<<<<< HEAD
    "favorite_url" => get_url("favorite.php"),
    "classes" => "btn btn-secondary"
];

// Build select dropdown options for column and order
$column_options = array_map(fn($c) => [$c => ucfirst($c)], $allowed_columns);
$order_options = array_map(fn($o) => [$o => strtoupper($o)], $sort_directions);
=======
    "classes" => "btn btn-secondary"
];
>>>>>>> 3d7eba7341e63905aaee348b9d5d3c7865c61bb7
?>

<div class="container-fluid">
    <h3>List Currencies</h3>
<<<<<<< HEAD
    <form method="POST" class="mb-3 row g-2">
        <!-- Search input -->
        <div class="col-md-2">
            <?php render_input(["type" => "search", "name" => "search", "label" => "Base Currency", "placeholder" => "Search Base Currency", "value" => $search]); ?>
        </div>
        
        <!-- Created Date filter -->
        <div class="col-md-2">
            <?php render_input(["type" => "date", "name" => "created_date", "label" => "Created Date", "value" => $created_date]); ?>
        </div>

        <!-- Sort Column options -->
        <div class="col-md-2">
            <?php render_input([
                "type" => "select",
                "name" => "column",
                "label" => "Sort Column",
                "options" => $column_options,
                "value" => $column
            ]); ?>
        </div>

        <!-- Sort Order options -->
        <div class="col-md-2">
            <?php render_input([
                "type" => "select",
                "name" => "order",
                "label" => "Order",
                "options" => $order_options,
                "value" => $order
            ]); ?>
        </div>

        <!-- Limit input -->
        <div class="col-md-2">
            <?php render_input(["type" => "number", "name" => "limit", "label" => "Limit", "value" => $limit, "rules" => ["min" => 1, "max" => 100]]); ?>
        </div>

        <!-- Submit button -->
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

    <!-- Table for displaying results -->
=======
    <form method="POST" class="mb-3">
        <?php render_input(["type" => "search", "name" => "search", "placeholder" => "Search Base Currency", "value" => $search]); ?>
        <?php render_input(["type" => "date", "name" => "created_date", "label" => "Created Date", "value" => se($_POST, "created_date", "", false)]); ?>
        <?php render_button(["text" => "Search", "type" => "submit", "classes" => "btn btn-primary"]); ?>
    </form>
>>>>>>> 3d7eba7341e63905aaee348b9d5d3c7865c61bb7
    <?php render_table($table); ?>
</div>

<?php
require_once(__DIR__ . "/../../../partials/flash.php");
?>
