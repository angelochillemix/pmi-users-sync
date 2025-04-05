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
	 * Returns the instance of the User Attribute Updater.
	 *
	 * @param string $class_name The name of the attribute updater class.
	 * @return Pmi_Users_Sync_User_Attribute_Updater|null The instance of the User Updater, or null if the class does not exist.
	 * @throws InvalidArgumentException If the class does not exist or does not implement the expected interface.
	 */
	public static function get_user_attribute_updater( string $class_name ): ?Pmi_Users_Sync_User_Attribute_Updater {
		if ( ! class_exists( $class_name ) ) {
			throw new InvalidArgumentException( "Class {$class_name} does not exist." );
		}

		if ( ! array_key_exists( $class_name, self::$user_attribute_updater_instances ) ) {
			$instance = new $class_name();
			if ( ! $instance instanceof Pmi_Users_Sync_User_Attribute_Updater ) {
				throw new InvalidArgumentException( "Class {$class_name} must implement Pmi_Users_Sync_User_Attribute_Updater." );
			}
			self::$user_attribute_updater_instances[ $class_name ] = $instance;
		}

		return self::$user_attribute_updater_instances[ $class_name ] ?? null;
	}
}
