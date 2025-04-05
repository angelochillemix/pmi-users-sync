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
class Test_Pmi_Users_Sync_Attribute_Updater_Factory extends TestCase {

	/**
	 * Test that the factory returns an instance of the expected class.
	 *
	 * @covers Pmi_Users_Sync_User_Attribute_Updater_Factory::get_user_attribute_updater
	 */
	public function testGetUserAttributeUpdaterReturnsInstance() {
		$class_name = 'Pmi_Users_Sync_User_Pmi_Id_Updater';
		$instance   = Pmi_Users_Sync_User_Attribute_Updater_Factory::get_user_attribute_updater( $class_name );
		$this->assertInstanceOf( Pmi_Users_Sync_User_Attribute_Updater::class, $instance );

		$class_name = 'Pmi_Users_Sync_User_Membership_Roles_Mapping_Updater';
		$instance   = Pmi_Users_Sync_User_Attribute_Updater_Factory::get_user_attribute_updater( $class_name );
		$this->assertInstanceOf( Pmi_Users_Sync_User_Attribute_Updater::class, $instance );

		$class_name = 'Pmi_Users_Sync_User_Memberships_Updater';
		$instance   = Pmi_Users_Sync_User_Attribute_Updater_Factory::get_user_attribute_updater( $class_name );
		$this->assertInstanceOf( Pmi_Users_Sync_User_Attribute_Updater::class, $instance );

		$class_name = 'Pmi_Users_Sync_User_Membership_Roles_Mapping_Updater';
		$instance   = Pmi_Users_Sync_User_Attribute_Updater_Factory::get_user_attribute_updater( $class_name );
		$this->assertInstanceOf( Pmi_Users_Sync_User_Attribute_Updater::class, $instance );
	}

	/**
	 * Test that the factory throws an exception when the class does not exist.
	 *
	 * @covers Pmi_Users_Sync_User_Attribute_Updater_Factory::get_user_attribute_updater
	 */
	public function testGetUserAttributeUpdaterThrowsExceptionWhenClassDoesNotExist() {
		$class_name = 'NonExistingClass';
		$this->expectException( InvalidArgumentException::class );
		Pmi_Users_Sync_User_Attribute_Updater_Factory::get_user_attribute_updater( $class_name );
	}

	/**
	 * Test that the factory throws an exception when the class does not implement the expected interface.
	 *
	 * @covers Pmi_Users_Sync_User_Attribute_Updater_Factory::get_user_attribute_updater
	 */
	public function testGetUserAttributeUpdaterThrowsExceptionWhenClassDoesNotImplementInterface() {
		$class_name = 'stdClass';
		$this->expectException( InvalidArgumentException::class );
		Pmi_Users_Sync_User_Attribute_Updater_Factory::get_user_attribute_updater( $class_name );
	}

	/**
	 * Test that the factory returns the same instance when called multiple times with the same class name.
	 *
	 * @covers Pmi_Users_Sync_User_Attribute_Updater_Factory::get_user_attribute_updater
	 */
	public function testGetUserAttributeUpdaterReturnsSameInstanceOnMultipleCalls() {
		$class_name = 'Pmi_Users_Sync_User_Roles_Updater';
		$instance1  = Pmi_Users_Sync_User_Attribute_Updater_Factory::get_user_attribute_updater( $class_name );
		$instance2  = Pmi_Users_Sync_User_Attribute_Updater_Factory::get_user_attribute_updater( $class_name );
		$this->assertSame( $instance1, $instance2 );
	}
}
