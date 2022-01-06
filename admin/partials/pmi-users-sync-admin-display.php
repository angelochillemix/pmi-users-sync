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

<html>

<head>

</head>

<body>
	<?php if ( $file_path ) { ?>
		<h1>PMI Users from Excel file</h1>
		<p><?php esc_html_e( 'Excel file path', 'pmi-users-sync' ); ?> <?php echo esc_html( $file_path ); ?></p>
	<?php } else { ?>
		<h1>PMI Users from PMI web service</h1>
	<?php } ?>
	<p><?php esc_html_e( 'Update the PMI ID of the users', 'pmi-users-sync' ); ?></p>
	<form id="update_users_form" method="POST">
		<input type="submit" name="update_users" value="<?php esc_html_e( 'Update', 'pmi-users-sync' ); ?>">Update the PMI-ID of the users</input>
		<?php wp_nonce_field( PMI_USERS_SYNC_PREFIX . 'nonce_action', PMI_USERS_SYNC_PREFIX . 'nonce_field' ); ?>
</form>

	</form>
	<br />
	<br />
	<?php if ( isset( $error_message ) && ! empty( $error_message ) ) { ?>
		<p> <?php echo esc_html( $error_message ); ?></p>
	<?php } ?>

	<?php if ( isset( $users ) ) { ?>
		<p><?php esc_html_e( 'Found ', 'pmi-users-sync' ); ?> <?php echo esc_html( count( $users ) ); ?> <?php echo esc_html( ' users' ); ?></p>
		<table class="styled-table">
			<thead>
				<th>PMI ID</th>
				<th>First name</th>
				<th>Last name</th>
				<th>Email</th>
			</thead>
			<?php foreach ( $users as $user ) : ?>
				<tr>
					<td><?php echo esc_html( $user->get_pmi_id() ); ?></td>
					<td><?php echo esc_html( $user->get_first_name() ); ?></td>
					<td><?php echo esc_html( $user->get_last_name() ); ?></td>
					<td><?php echo esc_html( $user->get_email() ); ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php } ?>
</body>

</html>
