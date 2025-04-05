<?php
/**
 * The class responsible to test {@see Pmi_Users_Sync_User_Updater}
 *
 * @link  http://angelochillemi.com
 * @since 1.3.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/tests
 */

use Yoast\WPTestUtils\BrainMonkey\TestCase;
/**
 * Test for {@see Pmi_Users_Sync_Pmi_User_Updater}
 */
class Test_Pmi_Users_Sync_Pmi_User_Updater extends TestCase {


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

	/**
	 * List of PMI ID used for testing
	 *
	 * @var array
	 */
	private $pmi_ids = array( '1234567', '2496408', '9876543', '1212121' );

	/**
	 * List of a different set of PMI ID used for testing
	 *
	 * @var array
	 */
	private $pmi_ids_difference_set = array( '0123456', '2496400', '9876541', '1212111' );

	/**
	 * Load the users from the Excel file
	 *
	 * @before
	 * @return void
	 */
	public function initialize_tests() {
		// $this->user_updater = Pmi_Users_Sync_User_Updater::get_user_updater();
		// $this->user_updater->register_user_attribute_updater( Pmi_Users_Sync_User_Pmi_Id_Updater::get_user_attribute_updater() );
		// $this->user_updater->register_user_attribute_updater( Pmi_Users_Sync_User_Roles_Updater::get_user_attribute_updater() );
		// $this->user_updater->register_user_attribute_updater( Pmi_Users_Sync_User_Memberships_Updater::get_user_attribute_updater() );
		$this->excel_loader = new Pmi_Users_Sync_Pmi_User_Excel_File_Loader( self::TEMP_PMI_EXCEL_FILE_PATH );
		$this->user_updater = Pmi_Users_Sync_User_Updater_Factory::create_user_updater();
		$this->users        = $this->excel_loader->load();

		// Create in the WP test database the users found in the Excel file
		foreach ( $this->users as $key => $value ) {
			$wp_error = wp_create_user(
				strtolower( $value->get_first_name() ),
				random_bytes( 8 ),
				$value->get_email()
			);
		}
	}

	/**
	 * Undocumented function
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
	 * Tests the initialization of users from the Excel file.
	 *
	 * Asserts that the loaded users are an array and verifies the count
	 * of users is 4. Additionally, checks that the 'dbem_pmi_id' meta field
	 * is not set for each user, ensuring that PMI-ID is not populated.
	 *
	 * @return void
	 */
	public function test_users_initialization() {
		$this->assertIsArray( $this->users );
		$this->assertCount( 4, $this->users, 'Not found 4 users in the test Excel file' );
		foreach ( $this->users as $key => $value ) {
			$user = get_user_by( 'email', $value->get_email() );
			$this->assertEmpty( get_user_meta( $user->ID, 'dbem_pmi_id', true ), 'PMI-ID should not be set' );
		}
	}

	/**
	 * Tests the creation and deletion of a user in the WordPress database.
	 *
	 * Creates a user with a given username, password, and email, and verifies
	 * that the user exists in the database with the correct username and email.
	 * Then deletes the user and verifies that the user no longer exists.
	 *
	 * @return void
	 */
	public function test_user_creation_and_deletion() {
		 $created_user = wp_create_user( 'test_username', 'test_password', 'test_email@email.it' );
		if ( is_wp_error( $created_user ) ) {
			$this->fail( 'Unable to create user in WP database' );
		}
		$user = get_user_by( 'email', 'test_email@email.it' );
		$this->assertInstanceOf( WP_User::class, $user, 'User does not exist' );
		$this->assertSame( 'test_username', $user->user_login, 'Username does not match' );
		$this->assertSame( 'test_email@email.it', $user->user_email, 'Email does not match' );
		wp_delete_user( $created_user );
	}

	/**
	 * Tests the update of all users' PMI-ID in the WordPress database.
	 *
	 * Applies the update process to all users with the given options,
	 * including the overwrite of existing PMI-IDs. Verifies that each
	 * user's PMI-ID in the database matches the expected value from the
	 * users' data.
	 *
	 * @return void
	 */
	public function test_all_users_update() {
		$options = array(
			Pmi_Users_Sync_Admin::OPTION_OVERWRITE_PMI_ID => true,
			Pmi_Users_Sync_Admin::OPTION_PMI_ID_CUSTOM_FIELD => 'dbem_pmi_id',
		);
		$this->user_updater->update( $this->users, $options );

		foreach ( $this->users as $key => $value ) {
			$user = get_user_by( 'email', $value->get_email() );
			$this->assertSame( $value->get_pmi_id(), get_user_meta( $user->ID, 'dbem_pmi_id', true ) );
		}
	}

	/**
	 * Tests the update of a single user's PMI-ID in the WordPress database.
	 *
	 * Applies the update process to the user with the given options,
	 * including the overwrite of existing PMI-IDs. Verifies that the
	 * user's PMI-ID in the database matches the expected value from the
	 * user's data.
	 *
	 * @return void
	 */
	public function test_single_user_update() {
		 $options           = array(
			 Pmi_Users_Sync_Admin::OPTION_OVERWRITE_PMI_ID => true,
			 Pmi_Users_Sync_Admin::OPTION_PMI_ID_CUSTOM_FIELD => 'dbem_pmi_id',
		 );
		 $user_ciccio_bello = $this->users[0];
		 $this->assertEquals( 'Ciccio', $user_ciccio_bello->get_first_name(), 'Not the same name' );
		 $this->assertEquals( 'test@email.it', $user_ciccio_bello->get_email(), 'Not the same email' );
		 $this->user_updater->update( $this->users, $options );
		 $user = get_user_by( 'email', 'test@email.it' );
		 $this->assertNotFalse( $user );
		 $this->assertInstanceOf( WP_User::class, $user, 'User does not exist' );
		 $this->assertSame( $this->pmi_ids[0], get_user_meta( $user->ID, 'dbem_pmi_id', true ) );
	}

	/**
	 * Tests the update of all users' PMI-ID in the WordPress database two times.
	 *
	 * Applies the update process to all users with the given options,
	 * including the overwrite of existing PMI-IDs. Verifies that each
	 * user's PMI-ID in the database matches the expected value from the
	 * users' data. Then repeats the update process and verifies that
	 * the results are the same.
	 *
	 * @return void
	 */
	public function test_users_update_two_times() {
		 $options = array(
			 Pmi_Users_Sync_Admin::OPTION_OVERWRITE_PMI_ID => true,
			 Pmi_Users_Sync_Admin::OPTION_PMI_ID_CUSTOM_FIELD => 'dbem_pmi_id',
		 );
		 $this->user_updater->update( $this->users, $options );
		 foreach ( $this->users as $key => $value ) {
			 $user = get_user_by( 'email', $value->get_email() );
			 $this->assertSame( $value->get_pmi_id(), get_user_meta( $user->ID, 'dbem_pmi_id', true ) );
		 }
		 $this->user_updater->update( $this->users, $options );
		 foreach ( $this->users as $key => $value ) {
			 $user = get_user_by( 'email', $value->get_email() );
			 $this->assertSame( $value->get_pmi_id(), get_user_meta( $user->ID, 'dbem_pmi_id', true ) );
		 }
	}

	/**
	 * Tests the update of all users' PMI-ID in the WordPress database with overwrite.
	 *
	 * Applies the update process to all users with the given options,
	 * including the overwrite of existing PMI-IDs. Verifies that each
	 * user's PMI-ID in the database matches the expected value from the
	 * users' data. Then repeats the update process with different PMI-ID
	 * and verifies that the results are the same.
	 *
	 * @return void
	 */
	public function test_users_update_with_overwrite() {
		$options = array(
			Pmi_Users_Sync_Admin::OPTION_OVERWRITE_PMI_ID => true,
			Pmi_Users_Sync_Admin::OPTION_PMI_ID_CUSTOM_FIELD => 'dbem_pmi_id',
		);
		$this->user_updater->update( $this->users, $options );
		foreach ( $this->users as $key => $value ) {
			$user = get_user_by( 'email', $value->get_email() );
			$this->assertSame( $value->get_pmi_id(), get_user_meta( $user->ID, 'dbem_pmi_id', true ) );
		}

		$new_excel_loader            = new Pmi_Users_Sync_Pmi_User_Excel_File_Loader( self::TEMP_PMI_EXCEL_FILE_DIFFERENT_PMI_ID_PATH );
		$users_with_different_pmi_id = $new_excel_loader->load();
		$this->user_updater->update( $users_with_different_pmi_id, $options );
		foreach ( $users_with_different_pmi_id as $key => $value ) {
			$user = get_user_by( 'email', $value->get_email() );
			$this->assertSame( $value->get_pmi_id(), get_user_meta( $user->ID, 'dbem_pmi_id', true ) );
		}
		foreach ( $this->users as $key => $value ) {
			$user = get_user_by( 'email', $value->get_email() );
			$this->assertNotSame( $value->get_pmi_id(), get_user_meta( $user->ID, 'dbem_pmi_id', true ) );
		}

		foreach ( $this->users as $key => $value ) {
			$user = get_user_by( 'email', $value->get_email() );
			delete_user_meta( $user->ID, 'dbem_pmi_id' );
		}
	}

	/**
	 * Tests the update of all users' PMI-ID in the WordPress database without overwrite.
	 *
	 * Applies the update process to all users with the given options,
	 * without the overwrite of existing PMI-IDs. Verifies that each
	 * user's PMI-ID in the database matches the expected value from the
	 * users' data. Then repeats the update process with different PMI-ID
	 * and verifies that the results are the same.
	 *
	 * @return void
	 */
	public function test_users_update_no_overwrite() {
		$options = array(
			Pmi_Users_Sync_Admin::OPTION_OVERWRITE_PMI_ID => false,
			Pmi_Users_Sync_Admin::OPTION_PMI_ID_CUSTOM_FIELD => 'dbem_pmi_id',
		);

		$this->assertFalse( boolval( $options[ Pmi_Users_Sync_Admin::OPTION_OVERWRITE_PMI_ID ] ) );
		foreach ( $this->users as $key => $value ) {
			$user = get_user_by( 'email', $value->get_email() );
			$this->assertEmpty( get_user_meta( $user->ID, $options[ PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field' ], true ), 'PMI-ID not empty' );
		}

		$this->user_updater->update( $this->users, $options );
		foreach ( $this->users as $key => $value ) {
			$user = get_user_by( 'email', $value->get_email() );
			$this->assertSame( $value->get_pmi_id(), get_user_meta( $user->ID, $options[ PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field' ], true ) );
		}

		$new_excel_loader            = new Pmi_Users_Sync_Pmi_User_Excel_File_Loader( self::TEMP_PMI_EXCEL_FILE_DIFFERENT_PMI_ID_PATH );
		$users_with_different_pmi_id = $new_excel_loader->load();
		foreach ( $users_with_different_pmi_id as $key => $value ) {
			$user = get_user_by( 'email', $value->get_email() );
			$this->assertNotSame( $value->get_pmi_id(), get_user_meta( $user->ID, $options[ PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field' ], true ), 'PMI-ID from test file ' . $value->get_pmi_id() . ', PMI-ID from DB ' . get_user_meta( $user->ID, 'dbem_pmi_id', true ) );
		}
		$this->user_updater->update( $this->users, $options );
		foreach ( $this->users as $key => $value ) {
			$user = get_user_by( 'email', $value->get_email() );
			$this->assertSame( $value->get_pmi_id(), get_user_meta( $user->ID, $options[ PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field' ], true ) );
		}
	}
}
