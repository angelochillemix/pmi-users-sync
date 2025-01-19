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
 * The plugin class responsible to update the user PMI ID
 *
 * This is used to update the PMI ID in the usermeta database according to the plugin settings
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 * @author     Angelo Chillemi <info@angelochillemi.com>
 */
class Pmi_Users_Sync_User_Pmi_Id_Updater extends Pmi_Users_Sync_User_Attribute_Updater {
	/**
	 * Update the PMI-ID of the user
	 *
	 * @param stdClass                $wp_user The user to update the PMI-ID for.
	 * @param Pmi_Users_Sync_Pmi_User $user The user to update the PMI-ID for.
	 * @param array                   $options The array with plugin settings.
	 * @return void
	 * @throws Exception If unable to perform the update.
	 */
	public function do_update( $wp_user, $user, $options ) {
		// Checking if plugin options are set and users from WordPress and PMI match, else do nothing.
		if ( ! array_key_exists( Pmi_Users_Sync_Admin::OPTION_PMI_ID_CUSTOM_FIELD, $options )
			|| ! $this->user_matched_condition( $wp_user, $user, $options ) ) {
			Pmi_Users_Sync_Logger::log_warning( sprintf( 'No match for %s', $wp_user->user_email ) );
			return;
		}

		try {
			if ( true === $this->pmi_id_to_be_updated( $user, $wp_user, $options ) ) {
				$result = update_user_meta( $wp_user->ID, $options[ Pmi_Users_Sync_Admin::OPTION_PMI_ID_CUSTOM_FIELD ], $user->get_pmi_id() );
				if ( true === $result || $result > 0 ) {
					$this->updated = true;
					Pmi_Users_Sync_Logger::log_information( __( 'PMI-ID of user with email ', 'pmi-users-sync' ) . $user->get_email() . __( ' updated to ', 'pmi-users-sync' ) . $options[ PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field' ] );
				} else {
					Pmi_Users_Sync_Logger::log_warning( __( 'PMI-ID custom field not updated', 'pmi-users-sync' ) );
				}
			} else {
				Pmi_Users_Sync_Logger::log_information( __( 'Update for user with email ', 'pmi-users-sync' ) . $user->get_email() . __( ' not done', 'pmi-users-sync' ) );
			}
		} catch ( Exception $exception ) {
			throw $exception;
		}
	}

	/**
	 * Check if the PMI-ID of the users should be updated based on the settings
	 *
	 * @param  Pmi_Users_Sync_Pmi_User $user    The user with the PMI-ID.
	 * @param  stdClass                $wp_user The {@see WP_User} instance of the user found with the specified email.
	 * @param  array                   $options The pluging settings.
	 * @return bool true if the PMI-ID is to be updated, false otherwise
	 */
	private function pmi_id_to_be_updated( $user, $wp_user, array $options ): bool {
		if ( $this->user_has_no_pmi_id( $wp_user, $options )
			|| ( ( true === boolval( $options[ Pmi_Users_Sync_Admin::OPTION_OVERWRITE_PMI_ID ] ) )
			&& ( ! $this->users_have_same_pmi_id( $wp_user, $user, $options ) ) )
		) {
			return true;
		}

		return false;
	}
}
