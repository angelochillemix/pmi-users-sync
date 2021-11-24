<?php

/**
 * Fired during plugin activation
 *
 * @link       http://angelochillemi.com
 * @since      1.0.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 * @author     Angelo Chillemi <info@angelochillemi.com>
 */
class Pmi_Users_Sync_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// Register the hook to the cron tasks
		if ( ! wp_next_scheduled( PMI_USERS_SYNC_CRON_HOOK ) ) {
			add_filter( 'cron_schedules', 'pus_monthly_intervals' ); 
			wp_schedule_event( time(), 'monthly', PMI_USERS_SYNC_CRON_HOOK );
		}
	}

	function pus_monthly_intervals($schedules) {
		$schedules['monthly'] = array(
			'interval' => 2635200,
			'display' => __('Once a month')
		);
		return $schedules;
	}

	/**
	 * Function called by cron on a monthly basis
	 *
	 * @return void
	 */
	public function pus_update_users_pmi_id() {
		// @todo TODO Make the filename dynamic
		$file_path = resource_path('/pmi-excel/' . Pmi_Users_Sync_Pmi_User_Excel_File_Loader::PMI_EXCEL_FILENAME);
		$loader = new Pmi_Users_Sync_Pmi_User_Excel_File_Loader($file_path);
		$users = $loader->load();

		if (isset($_POST['update_users'])) {
			$this->pmi_users_sync_users_update($users);
		}
	} 
	
	
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
