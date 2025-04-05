<?php
/**
 * The plugin class responsible to update the user PMI ID
 *
 * This is used to update the PMI ID in the usermeta database according to the plugin settings
 *
 * @link  http://angelochillemi.com
 * @since 1.5.0
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
class Pmi_Users_Sync_User_Membership_Roles_Mapping_Updater extends Pmi_Users_Sync_User_Attribute_Updater {
	/**
	 * Update the roles of the user according to the plugin settings
	 *
	 * @param stdClass                $wp_user The user to update the PMI-ID for.
	 * @param Pmi_Users_Sync_Pmi_User $user The user from PMI.
	 * @param array                   $options The array with plugin settings.
	 * @return void
	 */
	public function do_update( $wp_user, $user, $options ) {
		if ( ! $this->membership_role_mapping_option_set( $options ) ) {
			Pmi_Users_Sync_Logger::log_information( 'No membership roles mapping options found in the plugin settings.' );
			return;
		}

		$new_wp_user = get_userdata( $wp_user->ID );
		if ( empty( $new_wp_user ) ) {
			Pmi_Users_Sync_Logger::log_error( 'No user found with ID ' . $wp_user->ID );
			return;
		}

		$desired_roles = $this->get_desired_roles( $new_wp_user, $options );

		$current_roles = $new_wp_user->roles;
		$roles_to_add  = array_diff( $desired_roles, $current_roles );

		foreach ( $roles_to_add as $role ) {
			if ( ! $new_wp_user->has_role( $role ) ) {
				Pmi_Users_Sync_Logger::log_information( 'Adding role ' . $role . ' to user ' . $new_wp_user->ID );
				try {
					$new_wp_user->add_role( $role );
				} catch ( Exception $e ) {
					Pmi_Users_Sync_Logger::log_error( 'Cannot add role ' . $role . ' to user ' . $new_wp_user->ID . '. Error is: ' . $e->getMessage() );
				}
			}
		}
		Pmi_Users_Sync_Logger::log_information( 'Roles updated for user ' . $new_wp_user->ID );
		$this->updated = true;
	}

	/**
	 * Get the desired roles for a user based on their memberships and the plugin settings.
	 *
	 * @param WP_User $wp_user The WordPress user object.
	 * @param array   $options The plugin settings.
	 *
	 * @return array An array of unique role slugs that the user should have.
	 */
	private function get_desired_roles( $wp_user, $options ) {
		// Get the user's memberships from ACF.
		$user_memberships = Pmi_Users_Sync_Acf_Helper::get_user_memberships( $wp_user->ID );

		if ( empty( $user_memberships ) ) {
			return array( 'subscriber' );
		} // If there are no memberships, return Subscriber role by default

		// Initialize an empty array to store the desired roles.
		$desired_roles = array();

		// Loop through each membership slug.
		foreach ( $user_memberships as $membership_slug ) {
			// Get the roles mapping for the current membership slug from the plugin settings.
			$roles_mapping = $options[ Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_ROLES_MAPPING . '_' . $membership_slug ];

			// If the roles mapping is an array and not empty, add the roles to the desired roles array.
			if ( is_array( $roles_mapping ) && count( $roles_mapping ) > 0 ) {
				foreach ( $roles_mapping as $role_mapping_slug ) {
					$desired_roles[] = $role_mapping_slug;
				}
			}
		}
		return array_unique( $desired_roles );
	}

	/**
	 * Check if the options are set
	 *
	 * @param array $options The plugin settings.
	 * @return true if the options are set otherwise false.
	 */
	private function membership_role_mapping_option_set( $options ) {
		$memberships = Pmi_Users_Sync_Acf_Helper::get_memberships_settings();
		$options_set = false;
		foreach ( $memberships as $membership => $membership_slug ) {
			$options_set |= array_key_exists( Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_ROLES_MAPPING . '_' . $membership, $options );
		}
		return $options_set;
	}
}
