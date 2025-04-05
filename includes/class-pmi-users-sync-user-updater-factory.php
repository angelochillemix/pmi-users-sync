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
class Pmi_Users_Sync_User_Updater_Factory {

	/**
	 * No instances of this class.
	 */
	private function __construct() {
	}

	/**
	 * Returns the initialized instance of {@see Pmi_Users_Sync_User_Updater}.
	 *
	 * @return Pmi_Users_Sync_User_Updater The updater used to update the user: PMI-ID, roles and memberships.
	 */
	public static function create_user_updater() {
		$user_updater = Pmi_Users_Sync_User_Updater::get_user_updater();
		$user_updater->register_user_attribute_updater( Pmi_Users_Sync_User_Attribute_Updater_Factory::get_user_attribute_updater( Pmi_Users_Sync_User_Pmi_Id_Updater::class ) );
		$user_updater->register_user_attribute_updater( Pmi_Users_Sync_User_Attribute_Updater_Factory::get_user_attribute_updater( Pmi_Users_Sync_User_Roles_Updater::class ) );
		$user_updater->register_user_attribute_updater( Pmi_Users_Sync_User_Attribute_Updater_Factory::get_user_attribute_updater( Pmi_Users_Sync_User_Memberships_Updater::class ) );
		$user_updater->register_user_attribute_updater( Pmi_Users_Sync_User_Attribute_Updater_Factory::get_user_attribute_updater( Pmi_Users_Sync_User_Membership_Roles_Mapping_Updater::class ) );
		return $user_updater;
	}

	/**
	 * Returns the initialized instance of {@see Pmi_Users_Sync_User_Updater}
	 * configured to update only the roles of the user based on the membership/role
	 * mapping.
	 *
	 * @return Pmi_Users_Sync_User_Updater The updater used to update the user's roles
	 *                                     based on the membership/role map.
	 */
	public static function create_user_updater_for_membership_role_mapping() {
		$user_updater = Pmi_Users_Sync_User_Memberships_Roles_Updater::get_user_updater();
		$user_updater->register_user_attribute_updater( Pmi_Users_Sync_User_Attribute_Updater_Factory::get_user_attribute_updater( Pmi_Users_Sync_User_Membership_Roles_Mapping_Updater::class ) );
		return $user_updater;
	}
}
