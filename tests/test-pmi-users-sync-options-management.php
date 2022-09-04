<?php

use Yoast\WPTestUtils\BrainMonkey\TestCase;

/**
 * Undocumented class
 */
class Test_Pmi_Users_Sync_Options_Management extends TestCase {

	/**
	 * Test the add_option function of WordPress
	 *
	 * @return void
	 */
	public function test_add_option() {
		add_option( Pmi_Users_Sync_Admin::OPTION_USER_ROLE, 'test_role' );
		$option = get_option( Pmi_Users_Sync_Admin::OPTION_USER_ROLE );
		$this->assertEquals( 'test_role', $option );
		delete_option( Pmi_Users_Sync_Admin::OPTION_USER_ROLE );
		$this->assertFalse( get_option( Pmi_Users_Sync_Admin::OPTION_USER_ROLE ) );
	}

	/**
	 * Test the deletion of options to confirm the uninstallation works
	 *
	 * @return void
	 */
	public function test_delete_options_when_uninstall_plugin() {
		add_option( Pmi_Users_Sync_Admin::OPTION_OVERWRITE_PMI_ID, 'test_overwrite_pmi_id' );
		add_option( Pmi_Users_Sync_Admin::OPTION_USER_ROLE, 'test_user_role' );
		add_option( Pmi_Users_Sync_Admin::OPTION_USER_ROLE_TO_REMOVE, 'test_role_to_remove' );

		$this->assertEquals( 'test_overwrite_pmi_id', get_option( Pmi_Users_Sync_Admin::OPTION_OVERWRITE_PMI_ID ) );
		$this->assertEquals( 'test_user_role', get_option( Pmi_Users_Sync_Admin::OPTION_USER_ROLE ) );
		$this->assertEquals( 'test_role_to_remove', get_option( Pmi_Users_Sync_Admin::OPTION_USER_ROLE_TO_REMOVE ) );

		$pus_options = get_class_vars( Pmi_Users_Sync_Admin::class );
		foreach ( $pus_options as $pmi_users_sync_option => $pmi_users_sync_value ) {
			if ( str_starts_with( 'OPTION_', $pmi_users_sync_option ) ) {
				delete_option( $pmi_users_sync_value );
				$this->assertFalse( get_option( $pmi_users_sync_value ) );
			}
		}
	}
}
