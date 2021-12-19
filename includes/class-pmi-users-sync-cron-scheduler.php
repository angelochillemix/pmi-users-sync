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
class Pmi_Users_Sync_Cron_Scheduler
{
	/**
	 * Represents the hook function to setup the cron for the regular updated of the PMI-ID
	 */
	public const PMI_USERS_SYNC_CRON_HOOK = 'pus_cron_update_users_pmi_id';

	/**
	 * Schedule the regular updated of the PMI-ID
	 *
	 * @param string $recurrence
	 * @return void
	 */
	public function schedule(string $recurrence)
	{
		// Register the hook to the cron tasks
		if (!wp_next_scheduled(self::PMI_USERS_SYNC_CRON_HOOK)) {
			add_filter('cron_schedules', array($this, 'pus_add_intervals'));
			$error = wp_schedule_event(time(), $recurrence, self::PMI_USERS_SYNC_CRON_HOOK);
			add_action(self::PMI_USERS_SYNC_CRON_HOOK, array($this, 'pus_update_users_pmi_id'));
		}
	}

	/**
	 * Define the array of schedules for the cron tasks to synchronize the PMI-ID
	 *
	 * @param array $schedules The array with the WP defined recurrence
	 * @return array Return the $schedules array with in addition the monthly schedules as not part of standard supported recurrence
	 */
	public function pus_add_intervals($schedules)
	{
		$schedules['monthly'] = array(
			'interval' => 2635200,
			'display' => __('Once a month')
		);
		return $schedules;
	}

	/**
	 * Function called by cron on a regular basis. 
	 * WARNING: The name of the function must match the constant defined at class level {@see self::PMI_USERS_SYNC_CRON_HOOK}
	 *
	 * @return void
	 */
	public function pus_update_users_pmi_id()
	{
		$pmi_file_url = get_option(PMI_USERS_SYNC_PREFIX . 'pmi_file_field_id');

		try {
			// Return false if the file is not set in the plugin setting
			if (false !== $pmi_file_url) {
				$file_path = Path_Utils::get_file_path($pmi_file_url);
				Pmi_Users_Sync_Logger::logInformation(__('Loading PMI users from the file ' . $file_path), null);
				$loader = new Pmi_Users_Sync_Pmi_User_Excel_File_Loader($file_path);
				$users = $loader->load();
				Pmi_Users_Sync_Logger::logInformation(__('Synchronizing the PMI-ID of the users'), null);
				$this->pmi_users_sync_users_update($users);
			}
		} catch (Exception $exception) {
			Pmi_Users_Sync_Logger::logError(__('An error occurred while running the scheduled update. Error is: ') . $exception->getMessage(), null);
		}
	}

	/**
	 * Update the PMI-ID of the users
	 *
	 * @param Pmi_Users_Sync_Pmi_User[] $users The list of users for which to update the PMI-ID
	 * @return void
	 */
	private function pmi_users_sync_users_update($users)
	{
		$options = array();
		$options = [
			PMI_USERS_SYNC_PREFIX . 'overwrite_pmi_id' => get_option(PMI_USERS_SYNC_PREFIX . 'overwrite_pmi_id'),
			PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field' => get_option(PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field')
		];
		Pmi_Users_Sync_User_Updater::update($users, $options);
	}
}