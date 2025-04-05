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
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 * @author     Angelo Chillemi <info@angelochillemi.com>
 */
class Pmi_Users_Sync_User_Memberships_Roles_Updater extends Pmi_Users_Sync_User_Abstract_Updater {


	/**
	 * Represents the instance of the User Updater.
	 *
	 * @var Pmi_Users_Sync_User_Updater
	 */
	private static Pmi_Users_Sync_User_Memberships_Roles_Updater $instance;

	/**
	 * Returns the instance of the User Updater
	 *
	 * @return Pmi_Users_Sync_User_Memberships_Roles_Updater The instance of the User Updater.
	 */
	public static function get_user_updater() {
		if ( ! isset( self::$instance ) || null === self::$instance ) {
			self::$instance = new Pmi_Users_Sync_User_Memberships_Roles_Updater();
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
		// Retrieving list of WP users registered to the site.
		$wp_users = get_users(
			array(
				'count_total' => false,
				'fields'      => array( 'ID', 'user_login', 'user_email' ),
			)
		);

		if ( null === $wp_users ) {
			Pmi_Users_Sync_Logger::log_error( __( 'No users found to update', 'pmi-users-sync' ) );
			return;
		}

		// Looping the users list and update the user's attributes.
		foreach ( $wp_users as $wp_user ) {
			$updated = false;

			if ( null === $this->user_attibute_updaters || ! is_array( $this->user_attibute_updaters ) ) {
				Pmi_Users_Sync_Logger::log_error( __( 'User attribute updaters is null or not an array', 'pmi-users-sync' ) );
				continue;
			}

			foreach ( $this->user_attibute_updaters as $user_attibute_updater ) {
				if ( null === $user_attibute_updater ) {
					Pmi_Users_Sync_Logger::log_error( __( 'User attribute updater is null', 'pmi-users-sync' ) );
					continue;
				}

				$user_attibute_updater->update( $wp_user, $this->getEmptyPmiUser(), $options );
				$updated = $updated | $user_attibute_updater->is_updated();
			}
					// User updated. No need to loop all elements. Rest of users from PMI can be skipped.
					// In addition, we can remove the user already synchronized.
					// No update done. Log information.
			if ( false === boolval( $updated ) ) {
				Pmi_Users_Sync_Logger::log_information( __( 'User ', 'pmi-users-sync' ) . $wp_user->user_login . __( ' with email ', 'pmi-users-sync' ) . $wp_user->user_email . __( ' not updated', 'pmi-users-sync' ) );
			}
		}
	}

	/**
	 * Return an empty PMI user instance
	 *
	 * @return Pmi_Users_Sync_Pmi_User An empty PMI user instance
	 */
	private function getEmptyPmiUser() {
		return new Pmi_Users_Sync_Pmi_User( '', '', '', '' );
	}
}
