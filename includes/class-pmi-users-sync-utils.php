<?php
/**
 * Utility functions
 *
 * @link       http://angelochillemi.com/pmi-users-sync
 * @since      1.2.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 */

/**
 * Class with utilities methods
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 * @author     Angelo Chillemi <info@angelochillemi.com>
 */
class Pmi_Users_Sync_Utils {

	public const ACF_POST_TYPE = 'acf-field';

	/**
	 * No instances allowed since this class is meant to contain only static method
	 */
	private function __construct() {    }

	/**
	 * Return the full path of the file under the resources directory
	 *
	 * @param string $file_path The path of resources folder.
	 * @return string The full path of the file under the resources directory
	 */
	public static function resource_path( $file_path ) {
		return PMI_USERS_SYNC_PLUGIN_DIR_RESOURCES . $file_path;
	}

	/**
	 * Retieve the full path fo the file with the PMI-ID from PMI
	 *
	 * @param string $file_url The URL of the Excel file set in the plugin settings.
	 * @return bool|string The full path fo the file with the PMI-ID from PMI
	 */
	public static function get_file_path( $file_url ) {
		// Return false if the plugin setting is not set.
		if (
			! isset( $file_url )
			|| empty( $file_url )
			|| false === $file_url
		) {
			return false;
		}
		return self::attachment_url_to_path( $file_url );
	}

	/**
	 * Get the attachment absolute path from its url
	 *
	 * @param string $url the attachment url to get its absolute path.
	 * @param bool   $check_file If true it checks that the file exists, else return the resulting file path as string.
	 * @return bool|string It returns the absolute path of an attachment, or false if file does not exist
	 */
	public static function attachment_url_to_path( $url, $check_file = true ) {
		$parsed_url = wp_parse_url( $url );
		if ( empty( $parsed_url['path'] ) ) {
			return false;
		}
		// Remove parent directory.
		$dir_path = substr( ltrim( $parsed_url['path'], '/' ), strpos( ltrim( $parsed_url['path'], '/' ), '/' ) );
		// Remove one more trailing slash from the full path.
		$dir_path = ltrim( $dir_path, '/' );
		// Append the absolute path of WordPress directory from the ABSPATH variable.
		$file = ABSPATH . $dir_path;
		// Check if the resulting file exists and return its full path.
		if ( ! $check_file || file_exists( $file ) ) {
			return $file;
		}
		return false;
	}

	/**
	 * Check if a Advanced Custom Field PMI-ID field is defined
	 *
	 * @param string $field_name The name of the field to check existence for.
	 * @return bool true if the field is found, false otherwise
	 */
	public static function acf_field_exists( $field_name ) {
		global $wpdb;
		// $found      = false;
		// $acf_fields = wp_cache_get( PMI_USERS_SYNC_PREFIX . 'acf_field', '', false, $found );
		// if ( ! $found ) {
			// TODO #3 Avoid direct call to database.
			$acf_fields = $wpdb->get_results( $wpdb->prepare( "SELECT ID,post_parent,post_name FROM $wpdb->posts WHERE post_excerpt=%s AND post_type=%s", $field_name, self::ACF_POST_TYPE ) );
		// wp_cache_set( PMI_USERS_SYNC_PREFIX . 'acf_field', $acf_fields, '', 3600 );
		// }
		if ( is_null( $acf_fields ) ) {
			return false;
		}
		return ( count( $acf_fields ) ) > 0;
	}


}
