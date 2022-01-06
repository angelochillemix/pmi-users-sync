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
	 * Unschedule the event to update the PMI-ID
	 *
	 * @return void
	 * @since 1.2.0
	 */
	public function unschedule() {
		$timestamp = wp_next_scheduled( self::PMI_USERS_SYNC_CRON_HOOK );
		if ( $timestamp ) {
			// Unschedule the task.
			wp_unschedule_event( $timestamp, self::PMI_USERS_SYNC_CRON_HOOK );

			// Unregister the hook from the cron tasks.
			wp_clear_scheduled_hook( self::PMI_USERS_SYNC_CRON_HOOK );
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
		try {
			$pmi_id_custom_field_exists = Pmi_Users_Sync_Utils::acf_field_exists( get_option( Pmi_Users_Sync_Admin::OPTION_PMI_ID_CUSTOM_FIELD ) );
			if ( ! $pmi_id_custom_field_exists ) {
				Pmi_Users_Sync_Logger::log_warning( __( 'ACF field for PMI-ID does not exist', 'pmi-users-sync' ) );
				return;
			}

			$users = Pmi_Users_Sync_User_Loader_Factory::create_user_loader()->load();
			Pmi_Users_Sync_Logger::log_information( __( 'Synchronizing the PMI-ID of the users', 'pmi-users-sync' ) );
			$this->pmi_users_sync_users_update( $users );
		} catch ( Exception $exception ) {
			Pmi_Users_Sync_Logger::log_error( __( 'An error occurred while running the update. Error is: ', 'pmi-users-sync' ) . $exception->getMessage() );
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
