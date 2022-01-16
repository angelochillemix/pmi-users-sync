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
abstract class Pmi_Users_Sync_User_Attribute_Updater {
	/**
	 * The instance of this class.
	 *
	 * @var Pmi_Users_Sync_User_Attribute_Updater $instance Instance of this class.
	 */
	private static $instance;

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
	 * Update the user attribute.
	 *
	 * @param WP_User                   $wp_user The user to update the PMI-ID for.
	 * @param Pmi_Users_Sync_Pmi_User[] $users The user to update the PMI-ID for.
	 * @param array                     $options The array with plugin settings.
	 * @return void
	 */
	abstract public function update( $wp_user, $users, $options );

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
		if ( ! isset( self::$instance ) || null === self::$instance ) {
			$class          = get_called_class();
			self::$instance = new $class();
		}
		return self::$instance;
	}

	/**
	 * Undocumented function
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
