<?php
require(__DIR__ . "/../../partials/nav.php");

// Handle search input
//jjc88  05/1/2025 search and filter logic to show user their specificed search
$search = "";
$created_date = "";
$query = "SELECT id, base_currency, unit, XAU, XAG, PA, PL, GBP, EUR, created, modified, is_api 
          FROM `Currency`";
$params = [];

if (isset($_POST["search"])) {
    $search = se($_POST, "search", "", false);
    if (!empty($search)) {
        $query .= " WHERE base_currency LIKE :search";
        $params[":search"] = "%$search%";
    }
}
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



$query .= " ORDER BY created DESC LIMIT 25";

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
    //"edit_url" => get_url("admin/edit_currency.php"),
    //"delete_url" => get_url("admin/delete_currency.php"),
    "classes" => "btn btn-secondary"
];
?>

<div class="container-fluid">
    <h3>List Currencies</h3>
    <form method="POST" class="mb-3">
        <?php render_input(["type" => "search", "name" => "search", "placeholder" => "Search Base Currency", "value" => $search]); ?>
        <?php render_input(["type" => "date", "name" => "created_date", "label" => "Created Date", "value" => se($_POST, "created_date", "", false)]); ?>
        <?php render_button(["text" => "Search", "type" => "submit", "classes" => "btn btn-primary"]); ?>
    </form>
    <?php render_table($table); ?>
</div>

<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>
