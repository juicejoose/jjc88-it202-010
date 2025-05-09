<?php
//05/09/2025 jjc88 sort and filtering, limit 25, checkbox, applying role changes
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}

// apply update
if (isset($_POST["users"]) && isset($_POST["roles"])) {
    $user_ids = $_POST["users"];
    $role_ids = $_POST["roles"];
    if (empty($user_ids) || empty($role_ids)) {
        flash("Both users and roles need to be selected", "warning");
    } else {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO UserRoles (user_id, role_id, is_active) VALUES (:uid, :rid, 1) 
        ON DUPLICATE KEY UPDATE is_active = !is_active");
        foreach ($user_ids as $uid) {
            foreach ($role_ids as $rid) {
                try {
                    $stmt->execute([":uid" => $uid, ":rid" => $rid]);
                    flash("Updated role", "success");
                } catch (PDOException $e) {
                    flash(var_export($e->errorInfo, true), "danger");
                }
            }
        }
    }
}

// Get active roles
$active_roles = [];
$db = getDB();
$role_name = trim($_POST["role_name"] ?? "");
$role_query = "SELECT id, name, description FROM Roles WHERE is_active = 1";
$role_params = [];

if (!empty($role_name)) {
    $role_query .= " AND name LIKE :rname";
    $role_params[":rname"] = "%$role_name%";
}
$role_query .= " LIMIT 25"; // role search to 25 results

$stmt = $db->prepare($role_query);
try {
    $stmt->execute($role_params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($results) {
        $active_roles = $results;
    }
} catch (PDOException $e) {
    flash(var_export($e->errorInfo, true), "danger");
}

//  user by username
$users = [];
$username = trim(se($_POST, "username", "", false));
$query = "SELECT Users.id, username, 
    (SELECT GROUP_CONCAT(name, ' (' , IF(ur.is_active = 1,'active','inactive') , ')') 
     FROM UserRoles ur 
     JOIN Roles on ur.role_id = Roles.id 
     WHERE ur.user_id = Users.id) as roles
    FROM Users";

$params = [];
if (!empty($username)) {
    $query .= " WHERE username LIKE :username";
    $params[":username"] = "%$username%";
}
$query .= " LIMIT 25"; // Limit user search to 25 results

try {
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($results) {
        $users = $results;
    }
} catch (PDOException $e) {
    flash(var_export($e->errorInfo, true), "danger");
}
?>

<div class="container-fluid">
    <h1>Assign Roles</h1>
    <form method="POST">
        <div class="mb-3">
            <?php render_input([
                "type" => "text",
                "name" => "username",
                "id" => "username",
                "label" => "Username search",
                "value" => $username ?? "",
            ]); ?>
        </div>
        <div class="mb-3">
            <?php render_input([
                "type" => "text",
                "name" => "role_name",
                "id" => "role_name",
                "label" => "Role name search",
                "value" => $role_name ?? "",
            ]); ?>
        </div>
        <input type="hidden" name="action" value="fetch">
        <?php render_button(["text" => "Search", "type" => "submit"]); ?>
    </form>

    <form method="POST">
        <?php if (!empty($username)) : ?>
            <input type="hidden" name="username" value="<?php se($username, false); ?>" />
        <?php endif; ?>
        <?php if (!empty($role_name)) : ?>
            <input type="hidden" name="role_name" value="<?php se($role_name, false); ?>" />
        <?php endif; ?>
        <table class="table">
            <thead>
                <th>Users</th>
                <th>Roles to Assign</th>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <table class="table">
                            <!-- jjc88 05/09/2025 Logic for empty fields -->
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="2">No matching users found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $user) : ?>
                                    <tr>
                                        <td>
                                            <label for="user_<?php se($user, 'id'); ?>"><?php se($user, "username"); ?></label>
                                            <input id="user_<?php se($user, 'id'); ?>" type="checkbox" name="users[]" value="<?php se($user, 'id'); ?>" />
                                        </td>
                                        <td><?php se($user, "roles", "No Roles"); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </table>
                    </td>
                    <td>
                        <?php if (empty($active_roles)): ?>
                            <div>No matching roles found.</div>
                        <?php else: ?>
                            <?php foreach ($active_roles as $role) : ?>
                                <div>
                                    <label for="role_<?php se($role, 'id'); ?>"><?php se($role, "name"); ?></label>
                                    <input id="role_<?php se($role, 'id'); ?>" type="checkbox" name="roles[]" value="<?php se($role, 'id'); ?>" />
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php render_button(["text" => "Toggle Roles", "type" => "submit"]); ?>
    </form>
</div>

<?php
require_once(__DIR__ . "/../../../partials/flash.php");
?>
