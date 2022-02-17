<?php
/**
 * The class responsible to test {@see Pmi_Users_Sync_User_Memberships_Updater}
 *
 * @link  http://angelochillemi.com
 * @since 1.3.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 */

/**
 * The class responsible to test {@see Pmi_Users_Sync_User_Memberships_Updater}
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 * @author     Angelo Chillemi <info@angelochillemi.com>
 */
class Test_Pmi_Users_Sync_User_Memberships_Updater extends PHPUnit\Framework\TestCase {
	private const TEMP_PMI_EXCEL_FILE_PATH                  = __DIR__ . '/test-pmi-users.xls';
	private const TEMP_PMI_EXCEL_FILE_DIFFERENT_PMI_ID_PATH = __DIR__ . '/test-pmi-users-different-pmi-id.xls';

	/**
	 * Undocumented variable
	 *
	 * @var Pmi_Users_Sync_Pmi_User_Excel_File_Loader
	 */
	private $excel_loader;

	/**
	 * Undocumented variable
	 *
	 * @var Pmi_Users_Sync_User_Updater
	 */
	private $user_updater;

	/**
	 * Undocumented variable
	 *
	 * @var Pmi_Users_Sync_Pmi_User[]
	 */
	private $users = array();

	private $pmi_ids                = array( '1234567', '2496408', '9876543', '1212121' );
	private $pmi_ids_difference_set = array( '0123456', '2496400', '9876541', '1212111' );

	/**
	 * Initializes the tests
	 *
	 * @before
	 * @return void
	 */
	public function initialize_tests() {
		$this->user_updater = Pmi_Users_Sync_User_Updater::get_user_updater();
		$this->user_updater->register_user_attribute_updater( Pmi_Users_Sync_User_Attribute_Updater_Factory::get_user_attribute_updater( Pmi_Users_Sync_User_Pmi_Id_Updater::class ) );
		$this->user_updater->register_user_attribute_updater( Pmi_Users_Sync_User_Attribute_Updater_Factory::get_user_attribute_updater( Pmi_Users_Sync_User_Memberships_Updater::class ) );
		$this->excel_loader = new Pmi_Users_Sync_Pmi_User_Excel_File_Loader( self::TEMP_PMI_EXCEL_FILE_PATH );
		$this->users        = $this->excel_loader->load();

		$this->update_options();

		// Create in the WP test database the users found in the Excel file
		foreach ( $this->users as $key => $value ) {
			$wp_error = wp_create_user(
				strtolower( $value->get_first_name() ),
				random_bytes( 8 ),
				$value->get_email()
			);
		}
	}

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
	 * Undocumented function
	 *
	 * @return void
	 */
	public function test_single_user_update() {
		$user_ciccio_bello = $this->users[0];
		$this->assertEquals( 'Ciccio', $user_ciccio_bello->get_first_name(), 'Not the same name' );
		$this->assertEquals( 'test@email.it', $user_ciccio_bello->get_email(), 'Not the same email' );
		$this->assertSame( $this->pmi_ids[0], $user_ciccio_bello->get_pmi_id(), 'Not same PMI-ID' );
		$wp_user = get_user_by( 'email', 'test@email.it' );
		$this->assertNotFalse( $wp_user );
		$this->assertInstanceOf( WP_User::class, $wp_user, 'User does not exist and not an instance of WP_User' );

		$options = $this->get_options();

		// Setting PMI-CIC membership to the user.
		update_user_meta(
			$wp_user->ID,
			$options[ Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_CUSTOM_FIELD ],
			array(
				'PMI-CIC' => 'PMI-CIC',
			)
		);
		$this->user_updater->update( $this->users, $options );
		$this->assertSame( $this->pmi_ids[0], get_user_meta( $wp_user->ID, 'dbem_pmi_id', true ) );
		$user = get_user_by( 'email', 'test@email.it' );
		$this->assertInstanceOf( WP_User::class, $user, 'User does not exist' );
		$this->assertSame( $this->pmi_ids[0], get_user_meta( $user->ID, $options[ Pmi_Users_Sync_Admin::OPTION_PMI_ID_CUSTOM_FIELD ], true ) );
		$memberships = get_user_meta( $user->ID, $options[ Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_CUSTOM_FIELD ], true );
		$this->assertIsArray( $memberships, 'it is not an array' );
		$this->assertArrayHasKey( 'PMI', $memberships, 'PMI membership not found' );
		$this->assertArrayHasKey( 'PMI-SIC', $memberships, 'PMI-SIC membership not found' );
	}

	/**
	 * Returns the options for the synchronization of the PMI user.
	 *
	 * @return array plugin settings
	 */
	private function get_options() {
		$options = array(
			Pmi_Users_Sync_Admin::OPTION_OVERWRITE_PMI_ID => get_option( Pmi_Users_Sync_Admin::OPTION_OVERWRITE_PMI_ID ),
			Pmi_Users_Sync_Admin::OPTION_PMI_ID_CUSTOM_FIELD => get_option( Pmi_Users_Sync_Admin::OPTION_PMI_ID_CUSTOM_FIELD ),
			Pmi_Users_Sync_Admin::OPTION_USER_ROLE        => get_option( Pmi_Users_Sync_Admin::OPTION_USER_ROLE ),
			Pmi_Users_Sync_Admin::OPTION_USER_ROLE_TO_REMOVE => get_option( Pmi_Users_Sync_Admin::OPTION_USER_ROLE_TO_REMOVE ),
			Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_CUSTOM_FIELD => get_option( Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_CUSTOM_FIELD ),
			Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP       => get_option( Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP ),
			Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_TO_REMOVE => get_option( Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_TO_REMOVE ),
		);
		return $options;

	}
}
