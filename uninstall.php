<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       http://angelochillemi.com
 * @since      1.0.0
 *
 * @package    Pmi_Users_Sync
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

Pmi_Users_Sync_Logger::log_information( __( 'Uninstalling the plugin.', 'pmi-users-sync' ) );


// TODO #10 Delete all options stored by the plugin.
Pmi_Users_Sync_Logger::log_information( __( 'Removing options from WordPress options table.', 'pmi-users-sync' ) );
$pmi_users_sync_options = get_class_vars( Pmi_Users_Sync_Admin::class );
foreach ( $pus_options as $pmi_users_sync_option => $pmi_users_sync_value ) {
	if ( str_starts_with( 'OPTION_', $pmi_users_sync_option ) ) {
		Pmi_Users_Sync_Logger::log_information( __( 'Removing option ', 'pmi-users-sync' ) . $pmi_users_sync_option . __( ' from WordPress options table', 'pmi-users-sync' ) );
		delete_option( Pmi_Users_Sync_Admin::OPTION_DEP_SERVICE_PASSWORD );
	}
}
