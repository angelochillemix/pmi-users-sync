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
class Pmi_Users_Sync_User_Memberships_Updater extends Pmi_Users_Sync_User_Attribute_Updater {
	/**
	 * Update the roles of the user according to the plugin settings
	 *
	 * @param stdClass                $wp_user The user to update the PMI-ID for.
	 * @param Pmi_Users_Sync_Pmi_User $user The user to update the PMI-ID for.
	 * @param array                   $options The array with plugin settings.
	 * @return void
	 */
	public function do_update( $wp_user, $user, $options ) {
		if ( ! array_key_exists( Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP, $options )
			|| ! array_key_exists( Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_TO_REMOVE, $options ) ) {
			return;
		}
		// Get current user's memberships.
		$user_memberships = Pmi_Users_Sync_Acf_Helper::get_user_memberships( $wp_user->ID );

		$memberships_for_members      = is_array( $options[ Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP ] ) ? $options[ Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP ] : array();
		$memberships_to_remove_config = is_array( $options[ Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_TO_REMOVE ] ) ? $options[ Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_TO_REMOVE ] : array();
		$all_managed_memberships      = array_unique( array_merge( $memberships_for_members, $memberships_to_remove_config ) );

		if ( $this->user_matched_condition( $wp_user, $user, $options ) ) {
			// User is a member. Synchronize memberships to match settings.

			// Determine which of the managed memberships the user currently has.
			$current_managed_memberships = array_intersect( $user_memberships, $all_managed_memberships );

			// Memberships to add are the ones for members that the user doesn't have.
			$memberships_to_add = array_diff( $memberships_for_members, $user_memberships );

			// Memberships to remove are the managed memberships the user has but shouldn't have as a member.
			$memberships_to_remove = array_diff( $current_managed_memberships, $memberships_for_members );

			if ( ! empty( $memberships_to_add ) || ! empty( $memberships_to_remove ) ) {
				$new_user_memberships = array_merge( $user_memberships, $memberships_to_add );
				$new_user_memberships = array_diff( $new_user_memberships, $memberships_to_remove );
				$new_user_memberships = array_values( array_unique( $new_user_memberships ) );

				update_user_meta( $wp_user->ID, $options[ Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_CUSTOM_FIELD ], $new_user_memberships );
				$this->updated = true;
			}
		} else {
			// User is not a member. Remove all managed memberships.
			$memberships_to_remove = array_intersect( $user_memberships, $all_managed_memberships );
			if ( ! empty( $memberships_to_remove ) ) {
				$new_user_memberships = array_diff( $user_memberships, $memberships_to_remove );
				$new_user_memberships = array_values( array_unique( $new_user_memberships ) );
				update_user_meta( $wp_user->ID, $options[ Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_CUSTOM_FIELD ], $new_user_memberships );
				$this->updated = true;
			}
		}
	}
}
