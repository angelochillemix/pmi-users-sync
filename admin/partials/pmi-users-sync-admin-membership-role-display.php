<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://angelochillemi.com
 * @since      1.0.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! current_user_can( 'manage_options' ) ) {
	die( -1 );
}


/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://angelochillemi.com
 * @since      1.0.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<!DOCTYPE html>
<html lang="en">

<head>
	<title>Membership/Role Mapping</title>
</head>

<body>
	<div class="notice">
		<p><strong>Memberships/Roles Mapping:&nbsp;</strong><?php esc_html_e( 'Memberships/Roles Mapping can be set in the related settings page', 'pmi-users-sync' ); ?></p><p><a href="/wordpress/wp-admin/admin.php?page=pmi_users_sync_options&tab=membership_roles_settings_section"><?php esc_html_e( 'Click here to access the settings page', 'pmi-users-sync' ); ?></a></p>
	</div>
	<div class="notice notice-warning">
		<p><?php esc_html_e( 'If a user has no membership then it will be assigned the role subscriber by default', 'pmi-users-sync' ); ?></a></p>
	</div>

	<p><?php esc_html_e( 'Manually update the mapping of the memberships to the roles of the users based on the settings', 'pmi-users-sync' ); ?></p>
	<form id="update_users_form" method="POST">
		<input type="submit" name="membership_role_map" value="<?php esc_html_e( 'Map Membership/Role', 'pmi-users-sync' ); ?>"/>
		<?php wp_nonce_field( PMI_USERS_SYNC_PREFIX . 'nonce_action', PMI_USERS_SYNC_PREFIX . 'nonce_field' ); ?>
	</form>
	<br />
	<?php if ( ! empty( $pus_error_message ) ) : ?>
		<p><?php echo nl2br( esc_html( $pus_error_message ) ); ?></p>
	<?php endif; ?>

	<br />

	<?php if ( isset( $pus_last_synchronization_date ) && ! empty( $pus_last_synchronization_date ) ) { ?>
	<p>
		<?php
		/* translators: %s: Last synchronization date. */
		printf( esc_html__( 'Last synchronization on: %s', 'pmi-users-sync' ), esc_html( $pus_last_synchronization_date ) );
		?>
	</p>
	<?php } ?>

</body>

</html>
