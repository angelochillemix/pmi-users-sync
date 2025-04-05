<?php
/**
 * Fired during plugin activation to setup the cron scheduler to synchronize the PMI-ID from PMI with the users registered to the site
 *
 * @link  http://angelochillemi.com
 * @since 1.0.1
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
	 * Represents the callback to define custom schedules
	 */
	public const PMI_USERS_SYNC_CRON_CUSTOM_SCHEDULE_CALLBACK = 'pus_add_intervals';

	/**
	 * Represents the callback to call to update the PMI-ID on a regular basis
	 */
	public const PMI_USERS_SYNC_CRON_SCHEDULED_CALLBACK = 'pus_update_users_pmi_id';

	// Schedules constants that can be set from the plugin options:
	// daily, weekly, monthly, quarterly, every two minutes.
	public const PMI_USERS_SYNC_CRON_SCHEDULE_DAILY             = 'daily';
	public const PMI_USERS_SYNC_CRON_SCHEDULE_WEEKLY            = 'weekly';
	public const PMI_USERS_SYNC_CRON_SCHEDULE_MONTHLY           = 'pus_monthly';
	public const PMI_USERS_SYNC_CRON_SCHEDULE_QUARTERLY         = 'pus_quarterly';
	public const PMI_USERS_SYNC_CRON_SCHEDULE_EVERY_TWO_MINUTES = 'pus_every_two_minutes';

	// Default synchronization schedule.
	public const PMI_USERS_SYNC_CRON_SCHEDULE_DEFAULT = self::PMI_USERS_SYNC_CRON_SCHEDULE_MONTHLY;

	/**
	 * Mapping between recurrence option and seconds.
	 *
	 * @var array
	 */
	private $recurrence_in_seconds = array(
		self::PMI_USERS_SYNC_CRON_SCHEDULE_DAILY     => DAY_IN_SECONDS,
		self::PMI_USERS_SYNC_CRON_SCHEDULE_WEEKLY    => WEEK_IN_SECONDS,
		self::PMI_USERS_SYNC_CRON_SCHEDULE_MONTHLY   => MONTH_IN_SECONDS,
		self::PMI_USERS_SYNC_CRON_SCHEDULE_QUARTERLY => MONTH_IN_SECONDS * 3,
	);

	/**
	 * Schedule the regular updated of the PMI-ID
	 *
	 * @param  string $recurrence The recurrence of the event.
	 * @return void
	 */
	public function schedule( string $recurrence ) {
		if ( ! isset( $recurrence ) || is_null( $recurrence ) || empty( $recurrence ) ) {
			$recurrence = self::PMI_USERS_SYNC_CRON_SCHEDULE_DEFAULT;
		}

		if ( wp_get_schedule( self::PMI_USERS_SYNC_CRON_HOOK ) === $recurrence ) {
			// Do not schedule anything since same recurrence.
			return;
		}

		$this->clear_scheduled_hook();

		// Register the hook to the cron tasks.
		if ( ! wp_next_scheduled( self::PMI_USERS_SYNC_CRON_HOOK ) ) {
			$seconds = $this->get_seconds_from_schedule( $recurrence );
			$error   = wp_schedule_event(
				time() + $seconds, // Adding the recurrence in seconds otherwise it starts the synchronization immediately.
				$recurrence,
				self::PMI_USERS_SYNC_CRON_HOOK,
			);
			if ( is_object( $error ) && ( $error instanceof WP_Error ) && $error->has_errors() ) {
				Pmi_Users_Sync_Logger::log_error( __( 'An error occurred while scheduling the cron for the synchronization of the PMI-ID. Error is: ', 'pmi-users-sync' ) . $error->get_error_message() );
			}
		}
	}

	/**
	 * Returns the number of seconds as per recurrence argument passed.
	 *
	 * @param  string $recurrence The recurrence of the cron event.
	 * @return integer The seconds correspongin to the recurrence.
	 */
	private function get_seconds_from_schedule( $recurrence ) : int {
		$seconds = MONTH_IN_SECONDS; // Monthly recurrence by default.

		if ( key_exists( $recurrence, $this->recurrence_in_seconds ) ) {
			$seconds = $this->recurrence_in_seconds[ $recurrence ];
		}
		return $seconds;
	}

	/**
	 * Unschedule the event to update the PMI-ID
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function unschedule() {
		// Unschedule the task.
		$timestamp = wp_next_scheduled( self::PMI_USERS_SYNC_CRON_HOOK );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, self::PMI_USERS_SYNC_CRON_HOOK );
		}
	}

	/**
	 * Clear all scheduled events
	 *
	 * @return void
	 */
	public function clear_scheduled_hook() {
		$this->unschedule();
		// Unregister the hook from the WP cron.
		wp_clear_scheduled_hook( self::PMI_USERS_SYNC_CRON_HOOK );
	}

	/**
	 * Define the array of schedules for the cron tasks to synchronize the PMI-ID
	 *
	 * @param  array $schedules The array with the WP defined recurrence.
	 * @return array Return the $schedules array with in addition the monthly schedules as not part of standard supported recurrence
	 */
	public function pus_add_intervals( $schedules ) {
		$schedules[ self::PMI_USERS_SYNC_CRON_SCHEDULE_MONTHLY ]           = array(
			'interval' => MONTH_IN_SECONDS,
			'display'  => __( 'Once a month', 'pmi-users-sync' ),
		);
		$schedules[ self::PMI_USERS_SYNC_CRON_SCHEDULE_EVERY_TWO_MINUTES ] = array(
			'interval' => MINUTE_IN_SECONDS * 2,
			'display'  => __( 'Every 2 minutes', 'pmi-users-sync' ),
		);
		$schedules[ self::PMI_USERS_SYNC_CRON_SCHEDULE_QUARTERLY ]         = array(
			'interval' => MONTH_IN_SECONDS * 3,
			'display'  => __( 'Every quarter', 'pmi-users-sync' ),
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
			Pmi_Users_Sync_Logger::log_information( __( 'Run the scheduled synchronization of the PMI-ID of the users', 'pmi-users-sync' ) );
			$this->pmi_users_sync_users_update( $users );
		} catch ( Exception $exception ) {
			Pmi_Users_Sync_Logger::log_error( __( 'An error occurred while running the scheduled update. Error is: ', 'pmi-users-sync' ) . $exception->getMessage() );
		}
	}

	/**
	 * Update the PMI-ID of the users
	 *
	 * @param  Pmi_Users_Sync_Pmi_User[] $users The list of users for which to update the PMI-ID.
	 * @return void
	 */
	private function pmi_users_sync_users_update( $users ) {
		$options = array(
			Pmi_Users_Sync_Admin::OPTION_OVERWRITE_PMI_ID => get_option( Pmi_Users_Sync_Admin::OPTION_OVERWRITE_PMI_ID ),
			Pmi_Users_Sync_Admin::OPTION_PMI_ID_CUSTOM_FIELD => get_option( Pmi_Users_Sync_Admin::OPTION_PMI_ID_CUSTOM_FIELD ),
			Pmi_Users_Sync_Admin::OPTION_USER_ROLE        => get_option( Pmi_Users_Sync_Admin::OPTION_USER_ROLE ),
			Pmi_Users_Sync_Admin::OPTION_USER_ROLE_TO_REMOVE => get_option( Pmi_Users_Sync_Admin::OPTION_USER_ROLE_TO_REMOVE ),
		);

		$memberships = Pmi_Users_Sync_Acf_Helper::get_memberships_settings();
		foreach ( $memberships as $membership_slug ) {
			$options[ Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_ROLES_MAPPING . '_' . $membership_slug ] = get_option( Pmi_Users_Sync_Admin::OPTION_MEMBERSHIP_ROLES_MAPPING );
		}

		Pmi_Users_Sync_User_Updater::get_user_updater()->update( $users, $options );
	}
}
