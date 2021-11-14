<?php

/**
 * The plugin class responsible to update the user PMI ID
 *
 * This is used to update the PMI ID in the usermeta database according to the plugin settings
 *
 * @link       http://angelochillemi.com
 * @since      1.0.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 */

/**
 * The plugin class responsible to update the user PMI ID
 *
 * This is used to update the PMI ID in the usermeta database according to the plugin settings
 *
 * @since      1.0.0
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 * @author     Angelo Chillemi <info@angelochillemi.com>
 */
class Pmi_Users_Sync_User_Updater
{

    /**
     * @var Pmi_Users_Sync_Pmi_User[] Array of {@see Pmi_Users_Sync_User} instances which PMI ID must be updated 
     */
    private $users;

    /**
     * @var mixed The list of options to apply for the update of the PMI ID {@see ../admin/partials/pmi-users-sync-settings-page.php}
     */
    private $options;

    /**
     * Pmi_Users_Sync_User_Updater class constructur
     *
     * @param Pmi_Users_Sync_Pmi_User[] $users
     * @param mixed $options
     */
    public function __construct($users, $options)
    {
        $this->$users = $users;
    }

    /**
     * Update the PMI ID of the users
     *
     * @param Pmi_Users_Sync_Pmi_User[] $users
     * @param mixed $options
     * @return void
     */
    public static function update($users, $options)
    {
        foreach ($users as $user) {
            $wp_users = get_user_by('email', $user->get_email());
            if (false === $wp_users) {
                Pmi_Users_Sync_Logger::logInformation(__('User with email ' . $user->get_email() . ' not registered to the site'), array());
            }
            if ($wp_users && true == boolval($options[PMI_USERS_SYNC_PREFIX . 'overwrite_pmi_id'])) {
                $result = update_user_meta($wp_users->ID, PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field', $options[PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field']);
                if (true === $result) {
                    Pmi_Users_Sync_Logger::logInformation(__('PMI-ID of user with email updated to ' . $options[PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field']), array());
                }
                elseif (false === $result) {
                    Pmi_Users_Sync_Logger::logWarning(__('PMI-ID custom field does not exist'), array());
                } else {
                    Pmi_Users_Sync_Logger::logWarning(__('PMI-ID ' . $options[PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field'] . ' for user with email ' . $user->get_email() .  ' not updated'), array());
                }
            }
        }
    }
}
