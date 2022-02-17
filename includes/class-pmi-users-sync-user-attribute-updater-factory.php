<?php
/**
 * Represents the abstract class of the the factory class responsible to create user attributes updater instances.
 *
 * @link  http://angelochillemi.com
 * @since 1.3.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 */

/**
 * The plugin class responsible to create the instance of the user attribute updater.
 *
 * This is used to update the PMI ID in the usermeta database according to the plugin settings
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 * @author     Angelo Chillemi <info@angelochillemi.com>
 */
class Pmi_Users_Sync_User_Attribute_Updater_Factory {
	/**
	 * The instance of this class.
	 *
	 * @var Pmi_Users_Sync_User_Attribute_Updater $instance Instance of this class.
	 */
	private static $user_attribute_updater_instances = array();

	/**
	 * Class constructor.
	 */
	private function __construct() {
	}

	/**
	 * Returns the instance of the User Attribute Updater
	 *
	 * @param string $class The name of the attribute updater class.
	 * @return Pmi_Users_Sync_User_Attribute_Updater The instance of the User Updater.
	 */
	public static function get_user_attribute_updater( $class ): Pmi_Users_Sync_User_Attribute_Updater {
		$instance = null;
		if ( array_key_exists( $class, self::$user_attribute_updater_instances ) ) {
			$instance = self::$user_attribute_updater_instances[ $class ];
		}
		if ( ! isset( $instance ) || null === $instance ) {
			$instance = new $class();
			self::$user_attribute_updater_instances[ $class ] = $instance;
		}
		return $instance;
	}
}
