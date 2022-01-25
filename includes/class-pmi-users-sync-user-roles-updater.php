<?php
/**
 * The plugin class responsible to update the user PMI ID
 *
 * This is used to update the PMI ID in the usermeta database according to the plugin settings
 *
 * @link  http://angelochillemi.com
 * @since 1.3.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 */

/**
 * The plugin class responsible to update the user's roles according to the PMI and Chapter subscritpion
 *
 * This is used to update the roles of the user according to the plugin settings
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 * @author     Angelo Chillemi <info@angelochillemi.com>
 */
class Pmi_Users_Sync_User_Roles_Updater extends Pmi_Users_Sync_User_Attribute_Updater {
	/**
	 * Update the roles of the user according to the plugin settings
	 *
	 * @param stdClass                $wp_user The user to update the PMI-ID for.
	 * @param Pmi_Users_Sync_Pmi_User $user The user to update the PMI-ID for.
	 * @param array                   $options The array with plugin settings.
	 * @return void
	 */
	public function do_update( $wp_user, $user, $options ) {
		// Checking if plugin options are set, else do nothing.
		if ( ! array_key_exists( Pmi_Users_Sync_Admin::OPTION_USER_ROLE, $options )
			|| ! array_key_exists( Pmi_Users_Sync_Admin::OPTION_USER_ROLE_TO_REMOVE, $options ) ) {
			return;
		}

		$new_wp_user = new WP_User( $wp_user->ID );
		if ( empty( $new_wp_user ) ) {
			return;
		}
		if ( $this->user_matched_condition( $wp_user, $user, $options ) ) {
			// Retrieves the roles to set from the plugin settings.
			$roles = $options[ Pmi_Users_Sync_Admin::OPTION_USER_ROLE ];

			// Set the roles set in the plugin settings to the user.
			foreach ( $roles as $role ) {
				$new_wp_user->add_role( $role );
			}
		} else {

			// Retrieves the roles to set from the plugin settings.
			$roles = $options[ Pmi_Users_Sync_Admin::OPTION_USER_ROLE_TO_REMOVE ];

			// Remove the roles set in the plugin settings.
			foreach ( $roles as $role ) {
				$new_wp_user->remove_role( $role );
			}
		}
		$this->updated = true;
	}
}
