<?php

if (!defined('ABSPATH')) {
    exit();
}

/**
 * Provide a view for the settings of the plugin
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

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<?php

if (isset($_POST) && !empty($_POST)) {
    $overwrite_pmi_id_value = isset($_POST['overwrite_pmi_id']) ? $_POST['overwrite_pmi_id'] : '';
    update_option(PMI_USERS_SYNC_PREFIX . 'overwrite_pmi_id', $overwrite_pmi_id_value);
    update_option(PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field', $_POST['pmi_id_custom_field']);
}
?>

<html>

<head>

</head>

<body>
    <h1>Settings</h1>
    <form method="POST">
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">PMI-ID Priority</th>
                    <td>
                        <fieldset>
                            <label for="overwrite_pmi_id">
                                <input name="overwrite_pmi_id" type="checkbox" id="overwrite_pmi_id" value="1" <?php echo !empty(get_option(PMI_USERS_SYNC_PREFIX . 'overwrite_pmi_id')) ? 'checked' : '' ?>>
                                <?php _e('If checked, the PMI ID inserted by the users will be overwritten') ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">PMI-ID custom field</th>
                    <td>
                        <fieldset>
                            <input name="pmi_id_custom_field" type="text" id="pmi_id_custom_field" placeholder="<?php _e('Insert custom field name') ?>" size="16" value="<?php echo get_option(PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field') ?>">
                            <label for="pmi_id_custom_field">
                                <?php _e('Insert the PMI-ID custom field defined with ACF plugin (e.g. dbem_pmi_id)') ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes') ?>"></p>
    </form>
</body>

</html>