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

	public const ACF_POST_TYPE   = 'acf-field';
	public const ACF_POST_STATUS = 'publish';

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
		$post = self::get_acf_field( $field_name );
		return ( ! is_null( $post ) );
	}

	/**
	 * Return the WP_Post ACF custom field represented by the passed name of the field
	 *
	 * @param string $field_name The name of the custom field.
	 * @return The WP_Post ACF custom field represented by the passed name of the field.
	 */
	public static function get_acf_field( $field_name ) {
		$posts = self::get_acf_fields();
		foreach ( $posts as $post ) {
			if ( $field_name === $post->post_excerpt ) {
				return $post;
			}
		}
		return null;
	}


	/**
	 * Return an array of WP_Post representing the custom fields of ACF if a Advanced Custom Field PMI-ID field is defined
	 *
	 * @return WP_Post[] An array of WP_Post representing the custom fields of ACF if a Advanced Custom Field PMI-ID field is defined
	 */
	public static function get_acf_fields() : array {
		$cache_item_name = 'acf_fields';
		$posts           = wp_cache_get( $cache_item_name );

		if ( false === $posts ) {
			$args       = array(
				'post_type'   => self::ACF_POST_TYPE,
				'post_status' => self::ACF_POST_STATUS,
			);
			$acf_fields = new WP_Query( $args );
			$posts      = $acf_fields->get_posts();
			wp_cache_set( $cache_item_name, $posts, '', HOUR_IN_SECONDS );
		}
		return $posts;
	}


	/**
	 * Returns true if the matching conditions a user in WordPress and from PMI.
	 *
	 * @param stdClass                $wp_user The WP_User instance representing the user retrieved from WP database.
	 * @param Pmi_Users_Sync_Pmi_User $user The user retrieved from PMI.
	 * @param array                   $options The plugin settings.
	 * @return bool
	 */
	public static function user_matched_condition( $wp_user, $user, $options ): bool {
		return $wp_user->user_email === $user->get_email()
					|| self::users_have_same_pmi_id( $wp_user, $user, $options );
	}

	/**
	 * Check that user has no PMI-ID
	 *
	 * @param  WP_User $wp_user The registered {@see WP_User} to retrieve from WP database.
	 * @param  array   $options The plugin settings.
	 * @return boolean
	 */
	public static function user_has_no_pmi_id( $wp_user, $options ): bool {
		$pmi_id = get_user_meta(
			$wp_user->ID,
			$options[ Pmi_Users_Sync_Admin::OPTION_PMI_ID_CUSTOM_FIELD ],
			true
		);

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
	public static function users_have_same_pmi_id( $wp_user, $user, $options ): bool {
		$pmi_id = get_user_meta(
			$wp_user->ID,
			$options[ Pmi_Users_Sync_Admin::OPTION_PMI_ID_CUSTOM_FIELD ],
			true
		);

		return $pmi_id === $user->get_pmi_id();
	}
}
