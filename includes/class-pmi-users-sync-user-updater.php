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
     * Pmi_Users_Sync_User_Updater class constructor
     *
     * @param Pmi_Users_Sync_Pmi_User[] $users The list of PMI users
     * @param mixed $options The pluging settings
     */
    public function __construct($users, $options)
    {
        $this->$users = $users;
    }

    /**
     * Update the PMI ID of the users
     *
     * @param Pmi_Users_Sync_Pmi_User[] $users The list of PMI users
     * @param mixed $options The pluging settings
     * @return void
     */
    public static function update($users, $options)
    {
        foreach ($users as $user) {
            $wp_users = get_user_by('email', $user->get_email());

            if (false !== $wp_users) {
                $pmi_id = get_user_meta($wp_users->ID, PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field',true);
                if (true === self::pmi_id_to_be_updated($pmi_id, $wp_users, $options)) {
                    $result = update_user_meta($wp_users->ID, $options[PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field'], $user->get_pmi_id());
                    if (true === $result) {
                        Pmi_Users_Sync_Logger::logInformation(__('PMI-ID of user with email ' . $user->get_email() . ' updated to ' . $options[PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field']), array());
                    } elseif (false === $result) {
                        Pmi_Users_Sync_Logger::logWarning(__('PMI-ID custom field does not exist, therefore not updated'), array());
                    } else {
                        Pmi_Users_Sync_Logger::logWarning(__('PMI-ID ' . $options[PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field'] . ' for user with email ' . $user->get_email() .  ' did not exist'), array());
                    }
                } else {
                    Pmi_Users_Sync_Logger::logInformation(__('User with email ' . $user->get_email() . ' not overwritten'), array());
                }
            } else {
                Pmi_Users_Sync_Logger::logInformation(__('User with email ' . $user->get_email() . ' not registered to the site'), array());
            }
        }
    }

    /**
     * Check if the PMI-ID of the users should be updated based on the settings
     *
     * @param string $pmi_id The PMI-ID to set or false if not found in the user_meta table
     * @param WP_User $user The {@see WP:User} instance of the user found with the specified email
     * @param array $options The pluging settings
     * @return boolean true if the PMI-ID is to be updated, false otherwise
     */
    private static function pmi_id_to_be_updated(string $pmi_id, WP_User $user, array $options): bool
    {
        return (false === $pmi_id) || boolval($options[PMI_USERS_SYNC_PREFIX . 'overwrite_pmi_id']);
    }
}
