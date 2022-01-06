<?php
/**
 * Fired during plugin deactivation
 *
 * @link       http://angelochillemi.com
 * @since      1.0.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 * @author     Angelo Chillemi <info@angelochillemi.com>
 */
class Pmi_Users_Sync_Deactivator {

	/**
	 * Execute tasks for the deactivation of the plugin.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		// Unschedule the registered hook to update the PMI-ID.
		$scheduler = new Pmi_Users_Sync_Cron_Scheduler();
		$scheduler->unschedule();
	}

}
