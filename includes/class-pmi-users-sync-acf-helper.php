<?php
/**
 * The ACF Helper class
 *
 * @link       http://angelochillemi.com
 * @since      1.3.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/admin
 */

/**
 * The ACF Helper class
 *
 * Defines helper functions for Advanced Custom Fields plugin
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/admin
 * @author     Angelo Chillemi <info@angelochillemi.com>
 */
class Pmi_Users_Sync_Acf_Helper {

	/**
	 * No instances of this class.
	 */
	private function __construct() {
	}

	/**
	 * Returns the membership settings from the ACF plugin
	 *
	 * @return array
	 */
	public static function get_memberships_settings() {
		$parameters        = array(
			'post_type'    => 'acf-field',
			'post_excerpt' => get_option( Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_CUSTOM_FIELD ),
		);
		$query_memberships = new WP_Query( $parameters );

		if ( ! $query_memberships->have_posts() ) {
			return array();
		}

		return get_field_object( $query_memberships->post->post_name )['choices'];
	}

	/**
	 * Returns an array with all the memberships of the user
	 *
	 * @param int $user_id The ID of the use which the membership are retieved for.
	 * @return array array with all the memberships of the user
	 */
	public static function get_user_memberships( $user_id ) {
		// $user_memberships = get_field( get_option( Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_CUSTOM_FIELD ), 'user_' . $user_id );
		$user_memberships = get_user_meta( $user_id, get_option( Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_CUSTOM_FIELD ), true );
		return empty( $user_memberships ) ? array() : $user_memberships;
	}
}
