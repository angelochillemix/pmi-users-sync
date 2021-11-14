<?php

if (!defined('ABSPATH')) {
    exit();
}

if (!current_user_can('manage_options')) {
    die(-1);
}


/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://angelochillemi.com
 * @since      1.0.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/admin/partials
 */
?>

<?php
/* 
if (isset($_POST['update_users'])) {
    $file_path = resource_path('/pmi-excel/' . Pmi_Users_Sync_Pmi_User_Excel_File_Loader::PMI_EXCEL_FILENAME);
    $loader = new Pmi_Users_Sync_Pmi_User_Excel_File_Loader($file_path);
    $users = $loader->load();
    //var_dump('There are ' . count($users) . ' users');
    $options = array();
    $options = [
        PMI_USERS_SYNC_PREFIX . 'overwrite_pmi_id' => get_option(PMI_USERS_SYNC_PREFIX . 'overwrite_pmi_id'),
        PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field' => get_option(PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field')
    ];
    var_dump($options);
    die();
    $updater = Pmi_Users_Sync_User_Updater::update($users, $options);
}
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<html>

<head>

</head>

<body>
    <h1>PMI Users from Excel file</h1>
    <p>Update the PMI ID of the users </p>
    <form id="update_users_form" method="POST">
        <input type="submit" name="update_users" value="<?php echo _e('Update') ?>">Update PMI-ID</input>
    </form>
    <br />
    <br />
    <p>Excel file path <?php echo $file_path ?></p>
    <table class="styled-table">
        <thead>
            <th>PMI ID</th>
            <th>First name</th>
            <th>Last name</th>
            <th>Email</th>
        </thead>
        <?php foreach ($users as $user) : ?>
            <tr>
                <td><?php echo $user->get_pmi_id() ?></td>
                <td><?php echo $user->get_first_name() ?></td>
                <td><?php echo $user->get_last_name() ?></td>
                <td><?php echo $user->get_email() ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>