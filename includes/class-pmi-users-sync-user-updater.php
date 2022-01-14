<?php
/**
 * The plugin class responsible to update the user PMI ID
 *
 * This is used to update the PMI ID in the usermeta database according to the plugin settings
 *
 * @link  http://angelochillemi.com
 * @since 1.0.0
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
class Pmi_Users_Sync_User_Updater extends Pmi_Users_Sync_User_Abstract_Updater {


	/**
	 * Represents the instance of the User Updater.
	 *
	 * @var Pmi_Users_Sync_User_Updater
	 */
	private static Pmi_Users_Sync_User_Updater $instance;

	/**
	 * Avoid multiple instance of the User Updater.
	 */
	private function __construct() {
	}

	/**
	 * Returns the instance of the User Updater
	 *
	 * @return Pmi_Users_Sync_User_Updater The instance of the User Updater.
	 */
	public static function get_user_updater() {
		if ( ! isset( self::$instance ) || null === self::$instance ) {
			self::$instance = new Pmi_Users_Sync_User_Updater();
		}
		return self::$instance;
	}

	/**
	 * Update the PMI ID of the users
	 *
	 * @param  Pmi_Users_Sync_Pmi_User[] $users   The list of PMI users.
	 * @param  mixed                     $options The pluging settings.
	 * @return void
	 */
	protected function do_update( $users, $options ) {
		foreach ( $users as $user ) {
			$wp_users = get_user_by( 'email', $user->get_email() );

			if ( false !== $wp_users ) {
				if ( true === $this->pmi_id_to_be_updated( $user, $wp_users, $options ) ) {
					$result = update_user_meta( $wp_users->ID, $options[ Pmi_Users_Sync_Admin::OPTION_PMI_ID_CUSTOM_FIELD ], $user->get_pmi_id() );
					if ( true === $result ) {
						Pmi_Users_Sync_Logger::log_information( __( 'PMI-ID of user with email ', 'pmi-users-sync' ) . $user->get_email() . __( ' updated to ', 'pmi-users-sync' ) . $options[ PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field' ] );
					} elseif ( false === $result ) {
						Pmi_Users_Sync_Logger::log_warning( __( 'PMI-ID custom field does not exist, therefore not updated', 'pmi-users-sync' ) );
					} else {
						Pmi_Users_Sync_Logger::log_warning( __( 'PMI-ID ', 'pmi-users-sync' ) . $options[ PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field' ] . __( ' for user with email ', 'pmi-users-sync' ) . $user->get_email() . __( ' was not found', 'pmi-users-sync' ) );
					}
				} else {
					Pmi_Users_Sync_Logger::log_information( __( 'User with email ', 'pmi-users-sync' ) . $user->get_email() . __( ' not overwritten', 'pmi-users-sync' ) );
				}
			} else {
				Pmi_Users_Sync_Logger::log_information( __( 'User ', 'pmi-users-sync' ) . $user->get_first_name() . ' ' . $user->get_last_name() . __( ' with email ', 'pmi-users-sync' ) . $user->get_email() . __( ' not registered to the site', 'pmi-users-sync' ) );
			}
		}
	}

	/**
	 * Check if the PMI-ID of the users should be updated based on the settings
	 *
	 * @param  Pmi_Users_Sync_Pmi_User $user    The user with the PMI-ID.
	 * @param  WP_User                 $wp_user The {@see WP_User} instance of the user found with the specified email.
	 * @param  array                   $options The pluging settings.
	 * @return bool true if the PMI-ID is to be updated, false otherwise
	 */
	private function pmi_id_to_be_updated( Pmi_Users_Sync_Pmi_User $user, WP_User $wp_user, array $options ): bool {
		if ( $this->user_has_no_pmi_id( $wp_user, $options )
			|| ( ( true === boolval( $options[ Pmi_Users_Sync_Admin::OPTION_OVERWRITE_PMI_ID ] ) )
			&& ( ! self::user_has_same_pmi_id( $wp_user, $user, $options ) ) )
		) {
			return true;
		}

		return false;
	}

	/**
	 * Check that user has no PMI-ID
	 *
	 * @param  WP_User $wp_user The registered {@see WP_User} to retrieve from WP database.
	 * @param  array   $options The plugin settings.
	 * @return boolean
	 */
	private function user_has_no_pmi_id( $wp_user, $options ): bool {
		$pmi_id = get_user_meta( $wp_user->ID, $options[ PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field' ], true );

		// User meta not found or empty.
		return empty( $pmi_id );
	}

	/**
	 * Check that the two users have same PMI-ID
	 *
	 * @param  WP_User                 $wp_user The registered {@see WP_User} to retrieve from WP database.
	 * @param  Pmi_Users_Sync_Pmi_User $user    The user to synchronize.
	 * @param  array                   $options The plugin settings.
	 * @return boolean
	 */
	private static function user_has_same_pmi_id( WP_User $wp_user, Pmi_Users_Sync_Pmi_User $user, $options ): bool {
		$pmi_id = get_user_meta( $wp_user->ID, $options[ PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field' ], true );

		return $pmi_id === $user->get_pmi_id();
	}
}
