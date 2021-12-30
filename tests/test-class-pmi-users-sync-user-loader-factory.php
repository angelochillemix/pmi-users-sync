<?php
/**
 * Unit tests for {@see Pmi_Users_Sync_User_Loader_Factory}
 *
 * @link       http://angelochillemi.com/pmi-users-sync
 * @since      1.2.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/tests
 */

use PHPUnit\Framework\TestCase;

/**
 * Undocumented class
 */
class Test_Pmi_Users_Sync_User_Loader_Factory extends TestCase {

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function test_factory_excel_loader() {
		update_option( Pmi_Users_Sync_Admin::OPTION_USER_LOADER, Pmi_Users_Sync_Admin::OPTION_USER_LOADER_EXCEL );
		$loader = Pmi_Users_Sync_User_Loader_Factory::create_user_loader();
		$this->assertInstanceOf( 'Pmi_Users_Sync_Pmi_User_Excel_File_Loader', $loader, 'Not an instance of Pmi_Users_Sync_Pmi_User_Excel_File_Loader' );
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function test_factory_default_loader() {
		$loader = Pmi_Users_Sync_User_Loader_Factory::create_user_loader();
		$this->assertInstanceOf( 'Pmi_Users_Sync_Pmi_User_Excel_File_Loader', $loader, 'Not an instance of Pmi_Users_Sync_Pmi_User_Excel_File_Loader' );
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function test_factory_web_service_loader_without_username_password() {
		update_option( Pmi_Users_Sync_Admin::OPTION_USER_LOADER, Pmi_Users_Sync_Admin::OPTION_USER_WEB_SERVICE );
		$this->expectException( 'InvalidArgumentException' );
		$loader = Pmi_Users_Sync_User_Loader_Factory::create_user_loader();

		update_option( Pmi_Users_Sync_Admin::OPTION_DEP_SERVICE_USERNAME, 'test_username' );
		$this->expectException( 'InvalidArgumentException' );
		$loader = Pmi_Users_Sync_User_Loader_Factory::create_user_loader();

		// Delete option Pmi_Users_Sync_Admin::OPTION_DEP_SERVICE_USERNAME to test with only password.
		delete_option( Pmi_Users_Sync_Admin::OPTION_DEP_SERVICE_USERNAME, 'test_username' );
		update_option( Pmi_Users_Sync_Admin::OPTION_DEP_SERVICE_PASSWORD, 'test_password' );
		$this->expectException( 'InvalidArgumentException' );
		$loader = Pmi_Users_Sync_User_Loader_Factory::create_user_loader();
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function test_factory_web_service_with_username_password() {
		update_option( Pmi_Users_Sync_Admin::OPTION_DEP_SERVICE_USERNAME, 'test_username' );
		update_option( Pmi_Users_Sync_Admin::OPTION_DEP_SERVICE_PASSWORD, 'test_password' );
		$loader = Pmi_Users_Sync_User_Loader_Factory::create_user_loader();
		$this->assertInstanceOf( 'Pmi_Users_Sync_Pmi_User_Web_Service_Loader', $loader, 'Not an instance of Pmi_Users_Sync_Pmi_User_Web_Service_Loader' );
	}

}
