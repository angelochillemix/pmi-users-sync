<?php
/**
 * Fired during plugin activation to setup the cron scheduler to synchronize the PMI-ID from PMI with the users registered to the site
 *
 * @link       http://angelochillemi.com
 * @since      1.0.1
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 */

/**
 * Setup the cron scheduler to synchronize the PMI-ID from PMI with the users registered to the site
 */
class Pmi_Users_Sync_Cron_Scheduler {

	/**
	 * Represents the hook function to setup the cron for the regular updated of the PMI-ID
	 */
	public const PMI_USERS_SYNC_CRON_HOOK = 'pus_cron_update_users_pmi_id';

	/**
	 * Schedule the regular updated of the PMI-ID
	 *
	 * @param string $recurrence The recurrence of the event.
	 * @return void
	 */
	public function schedule( string $recurrence ) {
		// Register the hook to the cron tasks.
		if ( ! wp_next_scheduled( self::PMI_USERS_SYNC_CRON_HOOK ) ) {
			add_filter( 'cron_schedules', array( $this, 'pus_add_intervals' ) );
			$error = wp_schedule_event( time(), $recurrence, self::PMI_USERS_SYNC_CRON_HOOK );
			add_action( self::PMI_USERS_SYNC_CRON_HOOK, array( $this, 'pus_update_users_pmi_id' ) );
		}
	}

	/**
	 * Define the array of schedules for the cron tasks to synchronize the PMI-ID
	 *
	 * @param array $schedules The array with the WP defined recurrence.
	 * @return array Return the $schedules array with in addition the monthly schedules as not part of standard supported recurrence
	 */
	public function pus_add_intervals( $schedules ) {
		$schedules['monthly'] = array(
			'interval' => 2635200,
			'display'  => __( 'Once a month', 'pmi-users-sync' ),
		);
		return $schedules;
	}

	/**
	 * Function called by cron on a regular basis.
	 * WARNING: The name of the function must match the constant defined at class level {@see self::PMI_USERS_SYNC_CRON_HOOK}
	 *
	 * @return void
	 */
	public function pus_update_users_pmi_id() {
		 // TODO Check that ACF custom field exists.
		try {
			// $pmi_id_custom_field_exists = $this->acf_field_exists(get_option(self::OPTION_PMI_ID_CUSTOM_FIELD));
			// if (!$pmi_id_custom_field_exists) {
			// $error_message = __('PMI-ID custom field does not exist. Update not done!');
			// }

			$loader = Pmi_Users_Sync_User_Loader_Factory::create_user_loader();
			$users  = $loader->load();
			Pmi_Users_Sync_Logger::logInformation( __( 'Synchronizing the PMI-ID of the users', 'pmi-users-sync' ) );
			$this->pmi_users_sync_users_update( $users );
		} catch ( \PhpOffice\PhpSpreadsheet\Reader\Exception $exception ) {
			Pmi_Users_Sync_Logger::logError( __( 'An error occurred while reading the Excel file. Error is: ', 'pmi-users-sync' ) . $exception->getMessage() );
			$error_message = __( 'No file has been set in the plugin settings page or file does not exist.', 'pmi-users-sync' );
		} catch ( SoapFault $fault ) {
			Pmi_Users_Sync_Logger::logError( __( 'An error occurred while retrieving the list of PMI members through the web service. Error is: ', 'pmi-users-sync' ) . $fault->faultstring );
			$error_message = __( 'An error occurred while retrieving the list of PMI members through the web service.', 'pmi-users-sync' );
		} catch ( InvalidArgumentException $exception ) {
			Pmi_Users_Sync_Logger::logError( __( 'An error occurred. Error is: ', 'pmi-users-sync' ) . $exception->getMessage() );
			$error_message = __( 'An error occurred', 'pmi-users-sync' ) . ' ' . $exception->getMessage();
		} catch ( Exception $exception ) {
			Pmi_Users_Sync_Logger::logError( __( 'An error occurred while running the update. Error is: ', 'pmi-users-sync' ) . $exception->getMessage() );
			$error_message = __( 'An error occurred during the users update', 'pmi-users-sync' ) . ' ' . $exception->getMessage();
		}
	}

	/**
	 * Update the PMI-ID of the users
	 *
	 * @param Pmi_Users_Sync_Pmi_User[] $users The list of users for which to update the PMI-ID.
	 * @return void
	 */
	private function pmi_users_sync_users_update( $users ) {
		$options = array();
		$options = array(
			Pmi_Users_Sync_Admin::OPTION_OVERWRITE_PMI_ID => get_option( Pmi_Users_Sync_Admin::OPTION_OVERWRITE_PMI_ID ),
			Pmi_Users_Sync_Admin::OPTION_PMI_ID_CUSTOM_FIELD => get_option( Pmi_Users_Sync_Admin::OPTION_PMI_ID_CUSTOM_FIELD ),
		);
		Pmi_Users_Sync_User_Updater::update( $users, $options );
	}
}
