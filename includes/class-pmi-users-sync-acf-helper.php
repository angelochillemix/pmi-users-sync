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
	private const POST_FIELD_NAME_MEMBERSHIP = 'choices';

	/**
	 * No instances of this class.
	 */
	private function __construct() {}

	/**
	 * Returns the membership settings from the ACF plugin
	 *
	 * @return array
	 */
	public static function get_memberships_settings() {
		 // Check if ACF is active.
		if ( ! function_exists( 'get_field_object' ) || ! function_exists( 'acf_get_field' ) ) {
			Pmi_Users_Sync_Logger::log_error( 'ACF is not active. Cannot retrieve membership settings.' );
			return array();
		}

		$option_value = get_option( Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_CUSTOM_FIELD );
		if ( empty( $option_value ) ) {
			Pmi_Users_Sync_Logger::log_error( sprintf( 'Value for option "%s" is not set.', Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_CUSTOM_FIELD ) );
			return array();
		}

		$field             = acf_get_field( $option_value );
		$field_key_or_name = $field['key'];
		Pmi_Users_Sync_Logger::log_information( sprintf( 'ACF Field key "%s" found.', $field_key_or_name ) );

		if ( empty( $field_key_or_name ) ) {
			Pmi_Users_Sync_Logger::log_error( 'Membership custom field option is not set.' );
			return array();
		}

		$field = get_field_object( $field_key_or_name );

		if ( ! $field ) {
			Pmi_Users_Sync_Logger::log_error( sprintf( 'ACF Field with key or name "%s" not found.', $field_key_or_name ) );
			return array();
		}

		if ( isset( $field[ self::POST_FIELD_NAME_MEMBERSHIP ] ) && is_array( $field[ self::POST_FIELD_NAME_MEMBERSHIP ] ) ) {
			return $field[ self::POST_FIELD_NAME_MEMBERSHIP ];
		} else {
			Pmi_Users_Sync_Logger::log_error( sprintf( 'ACF Field with key or name "%s" does not have choices.', $field_key_or_name ) );
			return array();
		}
	}

	/**
	 * Returns an array with all the memberships of the user
	 *
	 * @param int $user_id The ID of the use which the membership are retieved for.
	 * @return array array with all the memberships of the user
	 */
	public static function get_user_memberships( $user_id ) {
		$user_memberships = get_user_meta( $user_id, get_option( Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_CUSTOM_FIELD ), true );
		return empty( $user_memberships ) ? array() : $user_memberships;
	}
}
