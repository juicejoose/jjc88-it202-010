<?php if (isset($data)) : ?>
    <?php
    // Setup some variables for readability
    $_extra_classes = se($data, "extra_classes", "", false);
    $_table_class = se($data, "table_class", "table", false);
    $_title = se($data, "title", "", false);
    $_data = isset($data["data"]) ? $data["data"] : [];
    if (!$_data) {
        $_data = [];
    }
    $_html_columns = isset($data["html_columns"]) ? (array)$data["html_columns"] : [];
    $_view_url = se($data, "view_url", "", false);
    $_view_label = se($data, "view_label", "View", false);
    $_view_classes = se($data, "view_classes", "btn btn-primary", false);
    $_edit_url = se($data, "edit_url", "", false);
    $_edit_label = se($data, "edit_label", "Edit", false);
    $_edit_classes = se($data, "edit_classes", "btn btn-secondary", false);
    $_delete_url = se($data, "delete_url", "", false);
    $_delete_label = se($data, "delete_label", "Delete", false);
    $_delete_classes = se($data, "delete_classes", "btn btn-danger", false);
    $_favorite_url = se($data, "favorite_url", "", false);
    $_favorite_label = se($data, "favorite_label", "Favorite", false);
    $_favorite_classes = se($data, "favorite_classes", "btn btn-warning", false);
    $_unfavorite_url = se($data, "unfavorite_url", "", false);
    $_unfavorite_label = se($data, "unfavorite_label", "Unfavorite", false);
    $_unfavorite_classes = se($data, "unfavorite_classes", "btn btn-warning", false);
    $_primary_key_column = se($data, "primary_key", "id", false);
    $_post_self_form = isset($data["post_self_form"]) ? $data["post_self_form"] : [];
    $_has_atleast_one_url = $_view_url || $_edit_url || $_delete_url || $_favorite_url || $_unfavorite_url || $_post_self_form;
    $_empty_message = se($data, "empty_message", "No records to show", false);
    $_header_override = isset($data["header_override"]) ? $data["header_override"] : [];
    $_columns = isset($data["columns"]) ? $data["columns"] : []; // <-- ADDED LINE
    $_ignored_columns = isset($data["ignored_columns"]) ? $data["ignored_columns"] : [];

    if (is_string($_header_override)) {
        $_header_override = explode(",", $_header_override);
    }

    if (is_string($_ignored_columns)) {
        $_ignored_columns = explode(",", $_ignored_columns);
    }

    if (!$_header_override && count($_data) > 0) {
        $_header_override = array_filter(array_keys($_data[0]), function ($v) use ($_ignored_columns) {
            return !in_array($v, $_ignored_columns);
        });
    }
    ?>
    <?php if ($_title) : ?>
        <h3><?php se($_title); ?></h3>
    <?php endif; ?>
    <table class="<?php se($_table_class); ?> <?php se($_extra_classes); ?>">
        <?php if ($_header_override) : ?>
            <thead>
                <tr>
                    <?php foreach ($_header_override as $h) : ?>
                        <th><?php se($_columns[$h] ?? $h); ?></th> <!-- updated line -->
                    <?php endforeach; ?>
                    <?php if ($_has_atleast_one_url) : ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
        <?php endif; ?>
        <tbody>
            <?php if (is_array($_data) && count($_data) > 0) : ?>
                <?php foreach ($_data as $row) : ?>
                    <tr>
                        <?php foreach ($row as $k => $v) : ?>
                            <?php if (!in_array($k, $_ignored_columns)) : ?>
                                <td><?php
                                    if (in_array($k, $_html_columns)) {
                                        echo $v; // assume trusted HTML
                                    } else {
                                        se($v); // assume untrusted data
                                    }
                                    ?>
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if ($_has_atleast_one_url) : ?>
                            <td>
                                <div class="d-flex gap-2 flex-wrap">
                                    <?php if ($_view_url) : ?>
                                        <a href="<?php se($_view_url); ?>?<?php se($_primary_key_column); ?>=<?php se($row, $_primary_key_column); ?>" class="<?php se($_view_classes); ?>"><?php se($_view_label); ?></a>
                                    <?php endif; ?>
                                    <?php if ($_edit_url) : ?>
                                        <a href="<?php se($_edit_url); ?>?<?php se($_primary_key_column); ?>=<?php se($row, $_primary_key_column); ?>" class="<?php se($_edit_classes); ?>"><?php se($_edit_label); ?></a>
                                    <?php endif; ?>
                                    <?php if ($_delete_url) : ?>
                                        <a href="<?php se($_delete_url); ?>?<?php se($_primary_key_column); ?>=<?php se($row, $_primary_key_column); ?>" class="<?php se($_delete_classes); ?>"><?php se($_delete_label); ?></a>
                                    <?php endif; ?>
                                    <?php if ($_favorite_url) : ?>
                                        <a href="<?php se($_favorite_url); ?>?<?php se($_primary_key_column); ?>=<?php se($row, $_primary_key_column); ?>" class="<?php se($_favorite_classes); ?>"><?php se($_favorite_label); ?></a>
                                    <?php endif; ?>
                                    <?php if ($_unfavorite_url) : ?>
                                        <a href="<?php se($_unfavorite_url); ?>?<?php se($_primary_key_column); ?>=<?php se($row, $_primary_key_column); ?>" class="<?php se($_unfavorite_classes); ?>"><?php se($_unfavorite_label); ?></a>
                                    <?php endif; ?>
                                    <?php if ($_post_self_form) : ?>
                                        <form method="POST">
                                            <input type="hidden" name="<?php se($_post_self_form, "name", $_primary_key_column); ?>" value="<?php se($row, $_primary_key_column); ?>" />
                                            <input type="submit" class="<?php se($_post_self_form, "classes"); ?>" value="<?php se($_post_self_form, "label", "Submit"); ?>" />
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="100%"><?php se($_empty_message); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php
unset(
    $_table_class,
    $_extra_classes,
    $_title,
    $_data,
    $_view_url,
    $_view_label,
    $_view_classes,
    $_edit_url,
    $_edit_label,
    $_edit_classes,
    $_delete_url,
    $_delete_label,
    $_delete_classes,
    $_favorite_url,
    $_favorite_label,
    $_favorite_classes,
    $_unfavorite_url,
    $_unfavorite_label,
    $_unfavorite_classes,
    $_primary_key_column,
    $_post_self_form,
    $_has_atleast_one_url,
    $_empty_message,
    $_header_override,
    $_ignored_columns,
    $_columns // <-- unsetting new variable
);
?>
