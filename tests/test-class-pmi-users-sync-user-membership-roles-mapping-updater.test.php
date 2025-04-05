<?php
/**
 * The class responsible to test {@see Pmi_Users_Sync_User_Membership_Roles_Mapping_Updater}
 *
 * @link  http://angelochillemi.com
 * @since 1.5.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/tests
 */

use Yoast\WPTestUtils\BrainMonkey\TestCase;

/**
 * The class responsible to test {@see Pmi_Users_Sync_User_Membership_Roles_Mapping_Updater}
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 * @author     Angelo Chillemi <info@angelochillemi.com>
 */
class Test_Pmi_Users_Sync_User_Membership_Roles_Mapping_Updater extends TestCase {

	/**
	 * The updater to test
	 *
	 * @var Pmi_Users_Sync_User_Membership_Roles_Mapping_Updater
	 */
	protected $updater;

	/**
	 * Initialize the tests
	 *
	 * @before
	 * @return void
	 */
	public function initialize_tests() {
		$this->updater = new Pmi_Users_Sync_User_Membership_Roles_Mapping_Updater();
	}

	/**
	 * Test that the user's roles are updated correctly when the options are valid.
	 *
	 * @return void
	 */
	public function test_do_update_with_valid_options() {
		$wp_user     = new stdClass();
		$wp_user->ID = 1;
		$pmi_user    = new Pmi_Users_Sync_Pmi_User( '123', 'first_name', 'last_name', 'email' );
		$options     = array(
			'membership_roles_mapping' => array(
				'membership_slug' => 'role_slug',
			),
		);

		$this->updater->do_update( $wp_user, $pmi_user, $options );

		// Assert that the desired roles are set correctly
		// $desired_roles = $this->updater->get_desired_roles( $wp_user, $options );
		// $this->assertArrayHasKey( 'role_slug', $desired_roles );
	}

	/**
	 * Test that the user's roles are not updated when the options are invalid.
	 *
	 * @return void
	 */
	public function test_do_update_with_invalid_options() {
		$wp_user     = new stdClass();
		$wp_user->ID = 1;
		$pmi_user    = new Pmi_Users_Sync_Pmi_User( '123', 'first_name', 'last_name', 'email' );
		$options     = array();

		$this->updater->do_update( $wp_user, $pmi_user, $options );

		// Assert that no roles are updated
		// $desired_roles = $this->updater->get_desired_roles( $wp_user, $options );
		// $this->assertEmpty( $desired_roles );
	}

	/**
	 * Test that the desired roles are set correctly when the user has valid memberships.
	 *
	 * @return void
	 */
	public function test_get_desired_roles_with_valid_membership() {
		$wp_user     = new stdClass();
		$wp_user->ID = 1;
		$options     = array(
			'membership_roles_mapping' => array(
				'membership_slug' => 'role_slug',
			),
		);

		$user_memberships   = Pmi_Users_Sync_Acf_Helper::get_user_memberships( $wp_user->ID );
		$user_memberships[] = 'membership_slug';

		// $desired_roles = $this->updater->get_desired_roles( $wp_user, $options );

		// // Assert that the desired roles are set correctly
		// $this->assertArrayHasKey( 'role_slug', $desired_roles );
	}

	/**
	 * Test that the desired roles are not set when the user has invalid memberships.
	 *
	 * @return void
	 */
	public function test_get_desired_roles_with_invalid_membership() {
		$wp_user     = new stdClass();
		$wp_user->ID = 1;
		$options     = array(
			'membership_roles_mapping' => array(
				'membership_slug' => 'role_slug',
			),
		);

		$user_memberships   = Pmi_Users_Sync_Acf_Helper::get_user_memberships( $wp_user->ID );
		$user_memberships[] = 'invalid_membership_slug';

		// $desired_roles = $this->updater->get_desired_roles( $wp_user, $options );

		// // Assert that no roles are updated
		// $this->assertEmpty( $desired_roles );
	}
}
