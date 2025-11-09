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


	/** Test data */
	// PMI ID		First Name	Last Name	Primary Email
	// 1234567		Ciccio		Bello		test@email.it
	// 2496408		Angelo		Chillemi	2test@email.it
	// 9876543		Pinco		Pallino		3test@email.it
	// 1212121		Caio		Sempronio	4test@email.it

	private const TEMP_PMI_EXCEL_FILE_PATH                  = __DIR__ . '/test-pmi-users.xls';
	private const TEMP_PMI_EXCEL_FILE_DIFFERENT_PMI_ID_PATH = __DIR__ . '/test-pmi-users-different-pmi-id.xls';

	/**
	 * Undocumented variable
	 *
	 * @var Pmi_Users_Sync_Pmi_User_Excel_File_Loader
	 */
	private $excel_loader;

	/**
	 * The user updater
	 *
	 * @var Pmi_Users_Sync_User_Updater
	 */
	private $user_updater;

	/**
	 * The attribute updater
	 *
	 * @var Pmi_Users_Sync_User_Membership_Roles_Mapping_Updater
	 */
	private $attribute_updater;

	/**
	 * The array of PMI users
	 *
	 * @var Pmi_Users_Sync_Pmi_User[]
	 */
	private $users = array();

	/**
	 * Initializes the tests
	 *
	 * @before
	 * @return void
	 */
	public function initialize_tests() {
		$this->user_updater      = Pmi_Users_Sync_User_Updater::get_user_updater();
		$this->attribute_updater = new Pmi_Users_Sync_User_Membership_Roles_Mapping_Updater();
		$this->user_updater->register_user_attribute_updater( $this->attribute_updater );

		$this->update_options();
		$this->add_roles();

		// Create in the WP test database the users found in the test Excel file
		$this->excel_loader = new Pmi_Users_Sync_Pmi_User_Excel_File_Loader( self::TEMP_PMI_EXCEL_FILE_PATH );
		$this->users        = $this->excel_loader->load();
		foreach ( $this->users as $value ) {
			wp_create_user(
				strtolower( $value->get_first_name() ),
				random_bytes( 8 ),
				$value->get_email()
			);
		}
	}

	/**
	 * Updates the plugin options to use during the tests.
	 *
	 * In particular, it sets the custom field for the membership and PMI ID and the memberships to set and remove when
	 * the user is a PMI member or not.
	 *
	 * @return void
	 */
	private function update_options() {
		update_option(
			Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_CUSTOM_FIELD,
			'dbem_membership',
		);

		update_option(
			Pmi_Users_Sync_Admin::OPTION_PMI_ID_CUSTOM_FIELD,
			'dbem_pmi_id',
		);

		// The membership to set when synching users.
		update_option(
			Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP,
			array(
				'PMI'     => 'PMI',
				'PMI-SIC' => 'PMI-SIC',
			)
		);

		// The membership to remove when synching users is not a PMI member.
		update_option(
			Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_TO_REMOVE,
			array(
				'PMI-SIC' => 'PMI-SIC',
			)
		);

		// The membership roles mapping
		update_option(
			Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_ROLES_MAPPING . '_pmi',
			array(
				'pmi'        => 'pmi',
				'subscriber' => 'subscriber',
			)
		);

		update_option(
			Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_ROLES_MAPPING . '_pmi-sic',
			array(
				'pmi'        => 'pmi',
				'subscriber' => 'subscriber',
				'pmi-sic'    => 'pmi-sic',
			)
		);

		update_option(
			Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_ROLES_MAPPING . '_pmi-cic',
			array(
				'pmi'        => 'pmi',
				'subscriber' => 'subscriber',
				'pmi-cic'    => 'pmi-cic',
			)
		);

		update_option(
			Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_ROLES_MAPPING . '_pmi-nic',
			array(
				'pmi'        => 'pmi',
				'subscriber' => 'subscriber',
				'pmi-nic'    => 'pmi-nic',
			)
		);

		update_option(
			Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_ROLES_MAPPING . '_cnr',
			array(
				'subscriber' => 'subscriber',
				'cnr'        => 'cnr',
			)
		);
	}

	/**
	 * Adds the required custom roles for the test suite.
	 *
	 * @before
	 * @return void
	 */
	public function add_roles() {
		$wp_roles = new WP_Roles();
		$wp_roles->add_role( 'pmi', 'PMI' );
		$wp_roles->add_role( 'pmi-sic', 'PMI-SIC' );
		$wp_roles->add_role( 'pmi-nic', 'PMI-NIC' );
		$wp_roles->add_role( 'pmi-cic', 'PMI-CIC' );
		$wp_roles->add_role( 'cnr', 'CNR' );
	}

	/**
	 * Clean up properties at the end of each test execution.
	 *
	 * @after
	 * @return void
	 */
	public function clean_up() {
		foreach ( $this->users as $key => $value ) {
			$user_to_delete = get_user_by( 'email', $value->get_email() );
			wp_delete_user( $user_to_delete->ID );
		}
		$this->assertCount( 1, get_users(), 'More than 1 user left in the database' );
		unset( $this->users );
		unset( $this->excel_loader );
		unset( $this->user_updater );
	}

	/**
	 * Test that the user's roles are updated correctly when the options are valid.
	 *
	 * @return void
	 */
	public function test_do_update_with_valid_options() {
		// Test with only PMI and PMI-CIC membership
		$wp_user  = new WP_User( 1 );
		$pmi_user = new Pmi_Users_Sync_Pmi_User( '123', 'first_name', 'last_name', 'email' );

		update_user_meta(
			$wp_user->ID,
			get_option( Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_CUSTOM_FIELD ),
			array(
				'pmi'     => 'PMI',
				'pmi-cic' => 'PMI-CIC',
			)
		);

		$this->attribute_updater->do_update( $wp_user, $pmi_user, $this->get_options() );
		$desidered_roles = array( 'pmi', 'pmi-cic', 'subscriber', 'administrator' );
		$this->assertEquals( $desidered_roles, $wp_user->roles );

		unset( $wp_user );

		// Test with only PMI membership
		$wp_user = new WP_User( 2 );
		update_user_meta(
			$wp_user->ID,
			get_option( Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_CUSTOM_FIELD ),
			array(
				'pmi' => 'PMI',
			)
		);

		$this->attribute_updater->do_update( $wp_user, $pmi_user, $this->get_options() );
		$desidered_roles = array( 'pmi', 'subscriber', 'administrator' );
		$this->assertEquals( $desidered_roles, $wp_user->roles );

		unset( $wp_user );
		unset( $pmi_user );
	}

	/**
	 * Test that the user's roles are not updated when the options are invalid.
	 *
	 * @return void
	 */
	public function test_do_update_with_invalid_options() {
		$wp_user  = new WP_User( 1 );
		$pmi_user = new Pmi_Users_Sync_Pmi_User( '123', 'first_name', 'last_name', 'email' );
		$options  = array(
			get_option( Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_CUSTOM_FIELD ) => array(
				'membership_slug' => 'role_slug',
			),
		);

		update_user_meta(
			$wp_user->ID,
			get_option( Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_CUSTOM_FIELD ),
			array(
				'pmi'     => 'PMI',
				'pmi-cic' => 'PMI-CIC',
			)
		);

		$this->attribute_updater->do_update( $wp_user, $pmi_user, $options );
		$desidered_roles = array( 'pmi', 'pmi-cic', 'subscriber' );
		$this->assertNotEquals( $desidered_roles, $wp_user->roles );

		unset( $pmi_user );
	}

	/**
	 * Test that the desired roles are set correctly when the user has valid memberships.
	 *
	 * @return void
	 */
	public function test_get_desired_roles_with_valid_membership() {
		$wp_user  = new WP_User( 1 );
		$pmi_user = new Pmi_Users_Sync_Pmi_User( '123', 'first_name', 'last_name', 'email' );

		update_user_meta(
			$wp_user->ID,
			get_option( Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_CUSTOM_FIELD ),
			array(
				'pmi' => 'PMI',
			)
		);

		$this->attribute_updater->do_update( $wp_user, $pmi_user, $this->get_options() );
		$desidered_roles = array( 'pmi', 'subscriber' );
		$this->assertEquals( $desidered_roles, $wp_user->roles );
	}

	/**
	 * Test that the desired roles are not set when the user has invalid memberships.
	 *
	 * @return void
	 */
	public function test_get_desired_roles_with_invalid_membership() {
		$wp_user  = new WP_User( 1 );
		$pmi_user = new Pmi_Users_Sync_Pmi_User( '123', 'first_name', 'last_name', 'email' );

		update_user_meta(
			$wp_user->ID,
			get_option( Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_CUSTOM_FIELD ),
			array(
				'invalid_membership_slug' => 'invalid_membership',
			)
		);

		$this->attribute_updater->do_update( $wp_user, $pmi_user, $this->get_options() );
		$this->assertArrayNotHasKey( 'subscriber', $wp_user->roles );
		$this->assertArrayNotHasKey( 'pmi', $wp_user->roles );
		$this->assertArrayNotHasKey( 'pmi-sic', $wp_user->roles );
		$this->assertArrayNotHasKey( 'pmi-cic', $wp_user->roles );
		$this->assertArrayNotHasKey( 'pmi-nic', $wp_user->roles );
	}

	/**
	 * Returns the options for the synchronization of the PMI user.
	 *
	 * @return array plugin settings
	 */
	private function get_options() {
		$options = array(
			Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_ROLES_MAPPING . '_pmi' => get_option( Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_ROLES_MAPPING . '_pmi' ),
			Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_ROLES_MAPPING . '_pmi-sic' => get_option( Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_ROLES_MAPPING . '_pmi-sic' ),
			Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_ROLES_MAPPING . '_pmi-cic' => get_option( Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_ROLES_MAPPING . '_pmi-cic' ),
			Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_ROLES_MAPPING . '_pmi-nic' => get_option( Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_ROLES_MAPPING . '_pmi-nic' ),
			Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_ROLES_MAPPING . '_cnr' => get_option( Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_ROLES_MAPPING . '_cnr' ),

			Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_CUSTOM_FIELD => get_option( Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_CUSTOM_FIELD ),
		);
		return $options;
	}
}
