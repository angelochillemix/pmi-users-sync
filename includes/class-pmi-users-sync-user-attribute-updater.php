<?php
/**
 * The plugin class responsible to update the user attribute while synchronizing with users from PMI.
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
abstract class Pmi_Users_Sync_User_Attribute_Updater {
	/**
	 * The instance of this class.
	 *
	 * @var Pmi_Users_Sync_User_Attribute_Updater $instance Instance of this class.
	 */
	private static $user_attribute_updater_instances = array();

	/**
	 * It is true if the user's attribute has been updated, false otherwise.
	 *
	 * @var bool true if the user's attribute has been updated, false otherwise.
	 */
	protected $updated;

	/**
	 * Class constructor.
	 */
	private function __construct() {
		$this->updated = false;
	}


	/**
	 * Update the user's attribute according to the plugin settings
	 *
	 * @param stdClass                $wp_user The user to update the PMI-ID for.
	 * @param Pmi_Users_Sync_Pmi_User $user The user to update the PMI-ID for.
	 * @param array                   $options The array with plugin settings.
	 * @throws Exception If unable to perform the update.
	 */
	public function update( $wp_user, $user, $options ) {
		if ( ! $user instanceof Pmi_Users_Sync_Pmi_User ) {
			throw new Exception( 'Invalid argument passed. Expected Pmi_Users_Sync_Pmi_User.' );
		}
		if ( ! $wp_user instanceof stdClass ) {
			throw new Exception( 'Invalid argument passed. Expected stdClass.' );
		}
		$this->do_update( $wp_user, $user, $options );
	}

	/**
	 * Update the user attribute.
	 *
	 * @param WP_User                   $wp_user The user to update the PMI-ID for.
	 * @param Pmi_Users_Sync_Pmi_User[] $users The user to update the PMI-ID for.
	 * @param array                     $options The array with plugin settings.
	 * @return void
	 */
	abstract public function do_update( $wp_user, $users, $options );

	/**
	 * Confirms if the user's attribute has been updated.
	 *
	 * @return bool true if the user's attribute has been updated, false otherwise.
	 */
	public function is_updated(): bool {
		return $this->updated;
	}

	/**
	 * Returns the instance of the User Attribute Updater
	 *
	 * @return Pmi_Users_Sync_User_Attribute_Updater The instance of the User Updater.
	 */
	public static function get_user_attribute_updater(): Pmi_Users_Sync_User_Attribute_Updater {
		$class    = get_called_class();
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

	/**
	 * Returns true if the matching conditions a user in WordPress and from PMI.
	 *
	 * @param stdClass                $wp_user The WP_User instance representing the user retrieved from WP database.
	 * @param Pmi_Users_Sync_Pmi_User $user The user retrieved from PMI.
	 * @param array                   $options The plugin settings.
	 * @return bool
	 */
	protected function user_matched_condition( $wp_user, $user, $options ): bool {
		return $wp_user->user_email === $user->get_email()
					|| $this->users_have_same_pmi_id( $wp_user, $user, $options );
	}

	/**
	 * Check that user has no PMI-ID
	 *
	 * @param  WP_User $wp_user The registered {@see WP_User} to retrieve from WP database.
	 * @param  array   $options The plugin settings.
	 * @return boolean
	 */
	protected function user_has_no_pmi_id( $wp_user, $options ): bool {
		$pmi_id = get_user_meta( $wp_user->ID, $options[ Pmi_Users_Sync_Admin::OPTION_PMI_ID_CUSTOM_FIELD ], true );

		// User meta not found or empty.
		return empty( $pmi_id );
	}

	/**
	 * Check that the two users have same PMI-ID
	 *
	 * @param  stdClass                $wp_user The registered {@see WP_User} to retrieve from WP database.
	 * @param  Pmi_Users_Sync_Pmi_User $user    The user to synchronize.
	 * @param  array                   $options The plugin settings.
	 * @return boolean
	 */
	protected static function users_have_same_pmi_id( $wp_user, $user, $options ): bool {
		$pmi_id = get_user_meta( $wp_user->ID, $options[ Pmi_Users_Sync_Admin::OPTION_PMI_ID_CUSTOM_FIELD ], true );

		return $pmi_id === $user->get_pmi_id();
	}
}
