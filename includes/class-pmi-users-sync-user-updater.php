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
                if (true === self::pmi_id_to_be_updated($user, $wp_users, $options)) {
                    $result = update_user_meta($wp_users->ID, $options[PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field'], $user->get_pmi_id());
                    if (true === $result) {
                        Pmi_Users_Sync_Logger::logInformation(__('PMI-ID of user with email ' . $user->get_email() . ' updated to ' . $options[PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field']));
                    } elseif (false === $result) {
                        Pmi_Users_Sync_Logger::logWarning(__('PMI-ID custom field does not exist, therefore not updated'));
                    } else {
                        Pmi_Users_Sync_Logger::logWarning(__('PMI-ID ' . $options[PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field'] . ' for user with email ' . $user->get_email() .  ' was not found'));
                    }
                } else {
                    Pmi_Users_Sync_Logger::logInformation(__('User with email ') . $user->get_email() . __(' not overwritten'));
                }
            } else {
                Pmi_Users_Sync_Logger::logInformation(__('User ') . $user->get_first_name() . ' ' . $user->get_last_name() . __(' with email ') . $user->get_email() . __(' not registered to the site'));
            }
        }
    }

    /**
     * Check if the PMI-ID of the users should be updated based on the settings
     *
     * @param string $pmi_id The PMI-ID to set or false if not found in the user_meta table
     * @param Pmi_Users_Sync_Pmi_User $user The user list with the PMI-ID
     * @param WP_User $wp_user The {@see WP_User} instance of the user found with the specified email
     * @param array $options The pluging settings
     * @return bool true if the PMI-ID is to be updated, false otherwise
     */
    private static function pmi_id_to_be_updated(Pmi_Users_Sync_Pmi_User $user, WP_User $wp_user, array $options): bool
    {
        if (
            self::user_has_no_pmi_id($wp_user, $options)
            || ((true === boolval($options[Pmi_Users_Sync_Admin::OPTION_OVERWRITE_PMI_ID]))
                && (!self::user_has_same_pmi_id($wp_user, $user, $options)))
        ) {
            return true;
        }

        return false;
    }

    private static function user_has_no_pmi_id($wp_user, $options): bool
    {
        $pmi_id = get_user_meta($wp_user->ID, $options[PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field'], true);

        // User meta not found or empty
        return empty($pmi_id);
    }

    private static function user_has_same_pmi_id(WP_User $wp_user, Pmi_Users_Sync_Pmi_User $user, $options): bool
    {
        $pmi_id = get_user_meta($wp_user->ID, $options[PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field'], true);

        return $pmi_id === $user->get_pmi_id();
    }
}
