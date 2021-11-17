<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://angelochillemi.com
 * @since      1.0.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/admin
 * @author     Angelo Chillemi <info@angelochillemi.com>
 */
class Pmi_Users_Sync_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $pmi_users_sync    The ID of this plugin.
	 */
	private $pmi_users_sync;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $pmi_users_sync       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($pmi_users_sync, $version)
	{

		$this->pmi_users_sync = $pmi_users_sync;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pmi_Users_Sync_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pmi_Users_Sync_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->pmi_users_sync, plugin_dir_url(__FILE__) . 'css/pmi-users-sync-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pmi_Users_Sync_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pmi_Users_Sync_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->pmi_users_sync, plugin_dir_url(__FILE__) . 'js/pmi-users-sync-admin.js', array('jquery'), $this->version, false);
	}

	/**
	 * Build the admin menu using the Boo Settings Helper class
	 * @see https://github.com/boospot/boo-settings-helper
	 *
	 * @return void
	 */
	public function pmi_users_sync_add_admin_menu()
	{	
		// TODO Implement the construction of the admin menu using the {@see Boo_Settings_Helper} class
 		/* $config_array = array (

		);

		require_once(PMI_USERS_SYNC_PLUGIN_DIR_VENDOR . 'boo-settings-helper');
		$settings_helper = new Boo_Settings_Helper($config_array); */
	}

	public function add_menu_link()
	{
		$menu_page = add_menu_page(esc_html__('PMI Users Sync', 'pmi_users_sync'), esc_html__('PMI Users Sync', 'pmi_users_sync'), 'manage_options', PMI_USERS_SYNC_PREFIX . 'pmi_users_sync_options', array($this, 'pmi_users_list_page'), 'dashicons-id-alt');

		add_submenu_page(PMI_USERS_SYNC_PREFIX . 'pmi_users_sync_options', esc_html__('PMI Users', 'pmi_users_sync'), esc_html__('PMI Users', 'pmi_users_sync'), 'manage_options', PMI_USERS_SYNC_PREFIX . 'pmi_users_sync_options');
		add_submenu_page(PMI_USERS_SYNC_PREFIX . 'pmi_users_sync_options', esc_html__('Settings', 'pmi_users_sync'), esc_html__('Settings', 'pmi_users_sync'), 'manage_options', PMI_USERS_SYNC_PREFIX . 'pmi_users_sync_options_settings', array($this, 'pmi_users_sync_settings_page'));
	}

	/**
	 * Shows the list of users from the Excel file
	 *
	 * @return void
	 */
	public function pmi_users_list_page()
	{
		// @todo TODO Make the filename dynamic
		$file_path = resource_path('/pmi-excel/' . Pmi_Users_Sync_Pmi_User_Excel_File_Loader::PMI_EXCEL_FILENAME);
		$loader = new Pmi_Users_Sync_Pmi_User_Excel_File_Loader($file_path);
		$users = $loader->load();

		if (isset($_POST['update_users'])) {
			$this->pmi_users_sync_users_update($users);
		}
		require_once(plugin_dir_path(__FILE__) . 'partials/pmi-users-sync-admin-display.php');
	}

	private function pmi_users_sync_users_update($users)
	{
		$options = array();
		$options = [
			PMI_USERS_SYNC_PREFIX . 'overwrite_pmi_id' => get_option(PMI_USERS_SYNC_PREFIX . 'overwrite_pmi_id'),
			PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field' => get_option(PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field')
		];
		$updater = Pmi_Users_Sync_User_Updater::update($users, $options);
	}

	/**
	 * Shows the plugin settings
	 * 
	 * @return void
	 */
	public function pmi_users_sync_settings_page()
	{
		require_once(plugin_dir_path(__FILE__) . 'partials/pmi-users-sync-settings-page.php');
	}
}