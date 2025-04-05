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

	private const DATE_TIME_FORMAT = 'Y-M-d H:i T';

	/**
	 * The list of updaters to invoke to update the user's attributes.
	 *
	 * @var Pmi_Users_Sync_User_Attribute_Updater[]
	 */
	protected $user_attibute_updaters;

	/**
	 * The list of updaters class name registered.
	 *
	 * @var array
	 */
	protected $user_attibute_updaters_class_names;

	/**
	 * Undocumented variable
	 *
	 * @var string $last_synchronization_date_time The last synchronization date and time in the format 'Y-M-d H:i T'
	 */
	protected $last_synchronization_date_time;

	/**
	 * Class constructor. Registers the User Attribute Updater classes that will contributes to the synchronization
	 * of the WP Users with the PMI Users.
	 */
	protected function __construct() {
		$this->user_attibute_updaters         = array();
		$this->last_synchronization_date_time = '';
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
		$this->last_synchronization_date_time = gmdate( self::DATE_TIME_FORMAT, time() );
		update_option( Pmi_Users_Sync_Admin::LOADER_LAST_SYNCHRONIZATION_DATE_TIME, $this->last_synchronization_date_time );
	}

	/**
	 * Returns formatted date and time of last user update in following format 'Y-M-d H:i T'
	 *
	 * @return string the formatted date and time of last user update
	 */
	public function get_synchronization_date_time() {
		return $this->last_synchronization_date_time;
	}

	/**
	 * Register the user attribute updaters to be invoked during the update of the users.
	 *
	 * @param Pmi_Users_Sync_User_Attribute_Updater $user_attribute_updater The user attribute updater.
	 * @return void
	 */
	public function register_user_attribute_updater( Pmi_Users_Sync_User_Attribute_Updater $user_attribute_updater ) {
		if ( ! is_array( $this->user_attibute_updaters ) ) {
			$this->user_attibute_updaters = array();
		}
		if ( ! is_array( $this->user_attibute_updaters_class_names ) ) {
			$this->user_attibute_updaters_class_names = array();
		}

		$class_name = get_class( $user_attribute_updater );

		if ( in_array( $class_name, $this->user_attibute_updaters_class_names, true ) ) {
			return;
		}

		Pmi_Users_Sync_Logger::log_debug( sprintf( 'Registering user attribute updater %s', $class_name ) );

		array_push( $this->user_attibute_updaters, $user_attribute_updater );
		array_push( $this->user_attibute_updaters_class_names, $class_name );
	}

	/**
	 * Returns the list of updaters to invoke to update the user's attributes.
	 *
	 * @return string[] The list of updaters to invoke to update the user's attributes.
	 */
	public function get_user_attribute_updaters() {
		if ( ! is_array( $this->user_attibute_updaters ) ) {
			return array();
		}
		return array_map( 'get_class', $this->user_attibute_updaters );
	}

	/**
	 * Returns the list of updaters to invoke to update the user's attributes.
	 *
	 * @return string[] The list of updaters to invoke to update the user's attributes.
	 */
	public function get_user_attribute_updaters_class_name() {
		if ( ! is_array( $this->user_attibute_updaters_class_names ) ) {
			return array();
		}
		return $this->user_attibute_updaters_class_names;
	}
}
