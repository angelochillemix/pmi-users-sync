<?php
/**
 * The file that defines the User Loader factory
 *
 * @link       http://angelochillemi.com/pmi-users-sync
 * @since      1.1.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 */

/**
 * Represents the PMI DEPService web service called to retrieve the list of members of the PMI Chapter in CSV format
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 * @author     Angelo Chillemi <info@angelochillemi.com>
 */
class Pmi_Users_Sync_User_Loader_Factory {

	/**
	 * Cache expire time of 4 days
	 */
	private const EXPIRE_TIME = 4 * DAY_IN_SECONDS;

	/**
	 * The key to cache the web service object
	 */
	private const CACHE_KEY = PMI_USERS_SYNC_PREFIX . 'PMI_MEMBERS_WEB_SERVICE';

	/**
	 * Private constructor
	 */
	private function __construct() {
	}

	/**
	 * Create a {@see Pmi_Users_Sync_User_Loader} loader based on the option selected by the user.
	 *
	 * @throws InvalidArgumentException If username and password for the web service are not set.
	 * @return Pmi_Users_Sync_User_Loader A loader based on the option selected by the user.
	 * Returns an Excel file loader by default.
	 */
	public static function create_user_loader() {
		$user_loader_option = get_option( Pmi_Users_Sync_Admin::OPTION_USER_LOADER );
		switch ( $user_loader_option ) {
			case 'option_web_service':
				$web_service = static::get_web_service();
					$loader  = new Pmi_Users_Sync_Pmi_User_Web_Service_Loader( $web_service );
				break;
			case 'option_excel':
			default: // default loading from Excel file.
				$pmi_file_url = get_option( Pmi_Users_Sync_Admin::OPTION_PMI_FILE_FIELD_ID );
				$file_path    = Pmi_Users_Sync_Path_Utils::get_file_path( $pmi_file_url );
				$loader       = new Pmi_Users_Sync_Pmi_User_Excel_File_Loader( $file_path );
				break;
		}
		return $loader;
	}

	/**
	 * Get an instance of the Pmi_Users_Sync_Members_Web_Service from the cache or create a new instance.
	 * Cache expiration time is 4 days (@see Pmi_Users_Sync_Members_Web_Service::EXPIRE_TIME).
	 *
	 * @return Pmi_Users_Sync_Members_Web_Service The web service instance as result of the call to PMI DEP Service
	 * @throws InvalidArgumentException If username and password for the web service are not set.
	 */
	private static function get_web_service() {
		$web_service = wp_cache_get( self::CACHE_KEY );
		if ( ! $web_service ) {
			$username = get_option( Pmi_Users_Sync_Admin::OPTION_DEP_SERVICE_USERNAME );
			$password = get_option( Pmi_Users_Sync_Admin::OPTION_DEP_SERVICE_PASSWORD );
			if ( $username && $password ) {
				$web_service = new Pmi_Users_Sync_Members_Web_Service( $username, $password );

				// Caching for 4 days the web service object to avoid repearing calls.
				wp_cache_set( self::CACHE_KEY, $web_service, 'web_service', intval( self::EXPIRE_TIME ) );
			} else {
				throw new InvalidArgumentException( 'Invalid username and password', 1 );
			}
		}
		return $web_service;
	}
}
