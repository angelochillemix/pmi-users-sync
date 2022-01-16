<?php
/**
 * The plugin class responsible to update the user PMI ID
 *
 * This is used to update the PMI ID in the usermeta database according to the plugin settings
 *
 * @link       http://angelochillemi.com
 * @since      1.3.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 */

/**
 * The plugin class responsible to update the user PMI ID
 *
 * This is used to update the PMI ID in the usermeta database according to the plugin settings
 *
 * @property Pmi_Users_Sync_User_Attribute_Updater[] $user_attibute_updaters The list of updaters to invoke to update the user's attributes.
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 * @author     Angelo Chillemi <info@angelochillemi.com>
 */
abstract class Pmi_Users_Sync_User_Abstract_Updater {

	/**
	 * The list of updaters to invoke to update the user's attributes.
	 *
	 * @var Pmi_Users_Sync_User_Attribute_Updater[]
	 */
	protected $user_attibute_updaters;

	/**
	 * Class constructor. Registers the User Attribute Updater classes that will contributes to the synchronization
	 * of the WP Users with the PMI Users.
	 */
	protected function __construct() {
		$this->user_attibute_updaters = array();
	}

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

	/**
	 * Register the user attribute updaters to be invoked during the update of the users.
	 *
	 * @param Pmi_Users_Sync_User_Attribute_Updater $user_attribute_updater The user attribute updater.
	 * @return void
	 */
	public function register_user_attribute_updater( Pmi_Users_Sync_User_Attribute_Updater $user_attribute_updater ) {
		array_push( $this->user_attibute_updaters, $user_attribute_updater );
	}

}
