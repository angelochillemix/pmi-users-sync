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
class Pmi_Users_Sync_User_Updater extends Pmi_Users_Sync_User_Abstract_Updater {


	/**
	 * Represents the instance of the User Updater.
	 *
	 * @var Pmi_Users_Sync_User_Updater
	 */
	private static Pmi_Users_Sync_User_Updater $instance;

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
		// Retrieving list of WP users registered to the site.
		$wp_users = get_users(
			array(
				'count_total' => false,
				'fields'      => array( 'ID', 'user_login', 'user_email' ),
			)
		);

		// Loop the entire users database to match them with those coming from Excel file or DEP Web Service, instead of the opposite
		// Match the email first. If email is not found, then match the PMI-ID:
		// If found
		// Based on the overwriting flag in the settings overwrite the PMI-ID
		// Assign the role and membership (i.e. Socio PMI and Socio PMI-SIC) to the user
		// If not found
		// Remove PMI-SIC role and membership from the user's profile, keeping PMI membership.

		// Looping the users list and update the user's attributes.
		foreach ( $wp_users as $wp_user ) {
			foreach ( $users as $user ) {
				$updated = false;
				foreach ( $this->user_attibute_updaters as $user_attibute_updater ) {
					$user_attibute_updater->update( $wp_user, $user, $options );
					$updated = $updated | $user_attibute_updater->is_updated();
				}
				if ( false === $updated ) {
					Pmi_Users_Sync_Logger::log_information( __( 'User ', 'pmi-users-sync' ) . $user->get_first_name() . ' ' . $user->get_last_name() . __( ' with email ', 'pmi-users-sync' ) . $user->get_email() . __( ' not registered to the site', 'pmi-users-sync' ) );
				}
			}
		}
	}
}
