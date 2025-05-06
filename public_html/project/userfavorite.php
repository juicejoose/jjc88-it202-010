<?php
require(__DIR__ . "/../../partials/nav.php");

// Get the current user ID (assuming you have user authentication)
$user_id = get_user_id(); // This function should return the ID of the logged-in user

// Handle search input
$search = "";
$query = "SELECT c.id, c.base_currency, c.unit, c.XAU, c.XAG, c.PA, c.PL, c.GBP, c.EUR, c.created, c.modified, c.is_api 
          FROM `User Currency Favorites` uf 
          JOIN `Currency` c ON uf.currency_id = c.id 
          WHERE uf.user_id = :user_id";  // Only fetch favorites for the current user
$params = [":user_id" => $user_id];

if (isset($_POST["search"])) {
    $search = se($_POST, "search", "", false);
    if (!empty($search)) {
        $query .= " AND c.base_currency LIKE :search";
        $params[":search"] = "%$search%";
    }
}

$query .= " ORDER BY c.created DESC LIMIT 25";

$db = getDB();
$stmt = $db->prepare($query);
$results = [];

try {
    $stmt->execute($params);
    $r = $stmt->fetchAll();
    if ($r) {
        $results = $r;
    } else {
        flash("No favorite currencies found", "warning");
    }
} catch (PDOException $e) {
    error_log("Error fetching favorite currencies " . var_export($e, true));
    flash("Unhandled error occurred", "danger");
}

// Table configuration for displaying the results
$table = [
    "data" => $results,
    "view_url" => get_url("entry.php"),
    "favorite_url" => get_url("unfavorite.php"), // Used to toggle favorite status
    "classes" => "table table-striped",
    "view_label" => "View",
    "view_classes" => "btn btn-primary",
    "favorite_label" => "Unfavorite", // Label changed to "Unfavorite"
    "favorite_classes" => "btn btn-warning"
];
?>

<div class="container-fluid">
    <h3>Your Favorite Currencies</h3>
    <form method="POST" class="mb-3">
        <?php render_input(["type" => "search", "name" => "search", "placeholder" => "Search Base Currency", "value" => $search]); ?>
        <?php render_button(["text" => "Search", "type" => "submit", "classes" => "btn btn-primary"]); ?>
    </form>
    <?php render_table($table); ?>
</div>

<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>
