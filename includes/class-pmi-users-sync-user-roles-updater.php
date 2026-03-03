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
	 * Converts an array of role names/slugs to an array of role slugs.
	 *
	 * @param array $roles_to_convert Array of role names or slugs.
	 * @param array $all_wp_roles All available WordPress roles.
	 * @return array Array of role slugs.
	 */
	private function convert_role_names_to_slugs( array $roles_to_convert, array $all_wp_roles ): array {
		$slugs = array();
		if ( empty( $roles_to_convert ) ) {
			return $slugs;
		}

		foreach ( $roles_to_convert as $role_or_name ) {
			if ( isset( $all_wp_roles[ $role_or_name ] ) ) {
				// It's already a slug.
				$slugs[] = $role_or_name;
			} else {
				// It's a name, find the slug.
				foreach ( $all_wp_roles as $slug => $details ) {
					if ( $details['name'] === $role_or_name ) {
						$slugs[] = $slug;
						break; // Found it, move to next role_or_name.
					}
				}
			}
		}
		return array_unique( $slugs );
	}
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

		$all_roles = wp_roles()->roles;

		$roles_for_members_config = is_array( $options[ Pmi_Users_Sync_Admin::OPTION_USER_ROLE ] ) ? $options[ Pmi_Users_Sync_Admin::OPTION_USER_ROLE ] : array();
		$roles_to_remove_config   = is_array( $options[ Pmi_Users_Sync_Admin::OPTION_USER_ROLE_TO_REMOVE ] ) ? $options[ Pmi_Users_Sync_Admin::OPTION_USER_ROLE_TO_REMOVE ] : array();

		$roles_for_members               = $this->convert_role_names_to_slugs( $roles_for_members_config, $all_roles );
		$roles_to_remove_for_non_members = $this->convert_role_names_to_slugs( $roles_to_remove_config, $all_roles );
		$all_managed_roles               = array_unique( array_merge( $roles_for_members, $roles_to_remove_for_non_members ) );

		if ( $this->user_matched_condition( $wp_user, $user, $options ) ) {
			// User is a member. Synchronize roles to match settings.
			$current_roles = $new_wp_user->roles;

			// Determine which of the managed roles the user currently has.
			$current_managed_roles = array_intersect( $current_roles, $all_managed_roles );

			// Roles to add are the ones for members that the user doesn't have.
			$roles_to_add = array_diff( $roles_for_members, $current_roles );

			// Roles to remove are the managed roles the user has but shouldn't have as a member.
			$roles_to_remove = array_diff( $current_managed_roles, $roles_for_members );

			foreach ( $roles_to_add as $role ) {
				$new_wp_user->add_role( $role );
				$this->updated = true;
			}

			foreach ( $roles_to_remove as $role ) {
				$new_wp_user->remove_role( $role );
				$this->updated = true;
			}
		} else {
			// User is not a member. Remove all managed roles.
			Pmi_Users_Sync_Logger::log_information( sprintf( 'Removing roles for users with email %s.', $wp_user->user_email ) );

			foreach ( $all_managed_roles as $role ) {
				if ( $new_wp_user->remove_role( $role ) ) {
					$this->updated = true;
				}
			}
		}
	}
}
