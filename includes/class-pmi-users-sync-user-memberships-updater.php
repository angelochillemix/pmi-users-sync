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

		if ( $this->user_matched_condition( $wp_user, $user, $options ) ) {
			// Update acf user membership field by user id.
			$new_user_memberships = $options[ Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP ];
			if ( is_array( $user_memberships ) && is_array( $new_user_memberships ) ) {
				$new_user_memberships = array_merge( $user_memberships, $new_user_memberships );
			}
			if ( is_array( $new_user_memberships ) ) {
				update_user_meta( $wp_user->ID, $options[ Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_CUSTOM_FIELD ], $new_user_memberships );
				$this->updated = true;
			}
		} else {
			// User not matched, therefore we remove the membership from the settings.
			$new_user_memberships = $options[ Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_TO_REMOVE ];
			if ( is_array( $user_memberships )
				&& is_array( $new_user_memberships )
				&& array_count_values( $user_memberships ) > 0 ) {
				$new_user_memberships = array_diff( $user_memberships, $new_user_memberships );
				update_user_meta( $wp_user->ID, $options[ Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_CUSTOM_FIELD ], $new_user_memberships );
				$this->updated = true;
			}
		}
	}
}
