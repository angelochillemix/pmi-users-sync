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
 * @since      1.3.0
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 * @author     Angelo Chillemi <info@angelochillemi.com>
 */
abstract class Pmi_Users_Sync_User_Abstract_Updater {

	/**
	 * Update the PMI ID of the users
	 *
	 * @param Pmi_Users_Sync_Pmi_User[] $users The list of PMI users.
	 * @param mixed                     $options The pluging settings.
	 * @return void
	 */
	abstract protected function do_update( $users, $options );

	/**
	 * Update the PMI ID of the users
	 *
	 * @param Pmi_Users_Sync_Pmi_User[] $users The list of PMI members.
	 * @param array                     $options The plugin options.
	 * @return void
	 */
	public function update( $users, $options ) {
		$this->do_update( $users, $options );

		// Store the date and time of the last synchronization.
		$date_time = gmdate( 'Y-M-d H:i T', time() );
		update_option( Pmi_Users_Sync_Admin::LOADER_LAST_SYNCHRONIZATION_DATE_TIME, $date_time );
	}

}
