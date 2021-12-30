<?php
/**
 * The interface that defined the structure of User Loader
 *
 * @link       http://angelochillemi.com
 * @since      1.0.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 */

/**
 *
 * This interface is used to define PMI User Loader from different sources.
 *
 * @since      1.0.0
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 * @author     Angelo Chillemi <info@angelochillemi.com>
 */
interface Pmi_Users_Sync_User_Loader {
	/**
	 * Loads the PMI users
	 *
	 * @return Pmi_Users_Sync_Pmi_User[] array of user instances
	 */
	public function load();
}
