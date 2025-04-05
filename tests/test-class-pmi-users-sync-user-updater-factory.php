<?php
/**
 * The class responsible to test {@see Pmi_Users_Sync_User_Updater_Factory}
 *
 * @link  http://angelochillemi.com
 * @since 1.5.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/tests
 */

use Yoast\WPTestUtils\BrainMonkey\TestCase;

/**
 * Test for {@see Pmi_Users_Sync_User_Updater_Factory}
 */
class Test_Pmi_Users_Sync_User_Updater_Factory extends TestCase {

	/**
	 * Test the factory method for creating the user updater returns an instance
	 * of the expected class.
	 *
	 * @covers Pmi_Users_Sync_User_Updater_Factory::create_user_updater
	 */
	public function testCreateUser_updaterReturnsInstance() {
		Pmi_Users_Sync_Logger::log_debug( 'testCreateUser_updaterReturnsInstance' );
		$user_updater = Pmi_Users_Sync_User_Updater_Factory::create_user_updater();
		$this->assertInstanceOf( Pmi_Users_Sync_User_Updater::class, $user_updater );
	}

	/**
	 * Test the factory method for creating the user updater returns an instance
	 * that registers the expected updaters.
	 *
	 * @covers Pmi_Users_Sync_User_Updater_Factory::create_user_updater
	 * @uses   Pmi_Users_Sync_User_Updater_Factory::get_user_attribute_updater
	 * @uses   Pmi_Users_Sync_User_Updater::get_user_attribute_updater
	 */
	public function testCreateUser_updaterRegistersExpectedUpdaters() {
		Pmi_Users_Sync_Logger::log_debug( 'testCreateUser_updaterRegistersExpectedUpdaters' );
		$user_updater      = Pmi_Users_Sync_User_Updater_Factory::create_user_updater();
		$expected_updaters = array(
			Pmi_Users_Sync_User_Pmi_Id_Updater::class,
			Pmi_Users_Sync_User_Memberships_Updater::class,
			Pmi_Users_Sync_User_Roles_Updater::class,
			Pmi_Users_Sync_User_Membership_Roles_Mapping_Updater::class,
		);
		$this->assertCount( 4, $user_updater->get_user_attribute_updaters_class_name() );
		$this->assertCount( 4, $user_updater->get_user_attribute_updaters() );
		$this->assertEquals( $expected_updaters, $user_updater->get_user_attribute_updaters_class_name() );
	}

	/**
	 * Test the factory method for creating the user updater returns the same
	 * instance of the User Updater when called multiple times.
	 *
	 * @covers Pmi_Users_Sync_User_Updater_Factory::create_user_updater
	 */
	public function testCreateUser_updaterReturnsSameInstanceOnMultipleCalls() {
		Pmi_Users_Sync_Logger::log_debug( 'testCreateUser_updaterReturnsSameInstanceOnMultipleCalls' );
		$user_updater1 = Pmi_Users_Sync_User_Updater_Factory::create_user_updater();
		$user_updater2 = Pmi_Users_Sync_User_Updater_Factory::create_user_updater();
		$this->assertSame( $user_updater1, $user_updater2 );
	}

	/**
	 * Test the factory method for creating the user updater returns a different
	 * instance when creating the user updater for membership role mapping.
	 *
	 * @covers Pmi_Users_Sync_User_Updater_Factory::create_user_updater_for_membership_role_mapping
	 * @covers Pmi_Users_Sync_User_Updater_Factory::create_user_updater
	 */
	public function testCreateUsersNotSameInstance() {
		$user_updater1 = Pmi_Users_Sync_User_Updater_Factory::create_user_updater_for_membership_role_mapping();
		$user_updater2 = Pmi_Users_Sync_User_Updater_Factory::create_user_updater();
		$this->assertNotSame( $user_updater1, $user_updater2 );
	}
}
