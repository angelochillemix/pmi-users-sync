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
	private $plugin_name;

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
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/pmi-users-sync-admin.css', array(), $this->version, 'all');
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

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/pmi-users-sync-admin.js', array('jquery'), $this->version, false);
	}

	public function notify_user_about_acf_plugin()
	{
		if (!is_plugin_active('advanced-custom-fields/acf.php')) {
			// Inform the user that this plugin needs ACF to create the custome PMI-ID field that will be added to the user's information
			ob_start() ?>
			<div class="notice notice-warning is-dismissible">
				<p><strong>Warning:&nbsp;</strong><?php esc_html_e('This plugin requires the plugin ', 'pmi-users-sync'); ?><a href="https://wordpress.org/plugins/advanced-custom-fields/"><?php _e('Advanced Custom Fields', 'pmi-users-sync'); ?></a></p>
			</div>
<?php
		echo ob_get_clean();
		}
	}


	/**
	 * Build the admin menu using the {@see Boo_Settings_Helper} class
	 * @see https://github.com/boospot/boo-settings-helper
	 *
	 * @return void
	 */
	public function add_menu_link()
	{
		/**
		 * Adding the main menu item at admin menu level
		 */
		$menu_page = add_menu_page(esc_html__('PMI Users Sync', 'pmi_users_sync'), esc_html__('PMI Users Sync', 'pmi_users_sync'), 'manage_options', PMI_USERS_SYNC_PREFIX . 'pmi_users_sync_options', array($this, 'pmi_users_list_page'), 'dashicons-id-alt');
		add_submenu_page(PMI_USERS_SYNC_PREFIX . 'pmi_users_sync_options', esc_html__('PMI Users', 'pmi_users_sync'), esc_html__('PMI Users', 'pmi_users_sync'), 'manage_options', PMI_USERS_SYNC_PREFIX . 'pmi_users_sync_options');

		/**
		 * Build the menu configuration array for the Boo Settings Helper class
		 */
		$config_array_menu = array(
			'prefix'   => PMI_USERS_SYNC_PREFIX,
			'tabs'     => true,
			'menu'     =>
			array(
				'page_title' => __('PMI Users Sync Settings', 'pmi-users-sync'),
				'menu_title' => __('Settings', 'pmi-users-sync'),
				'capability' => 'manage_options',
				'slug'       => 'pmi_users_sync_options',
				'icon'       => 'dashicons-id-alt',
				'position'   => 70,
				'parent'     => PMI_USERS_SYNC_PREFIX . 'pmi_users_sync_options',
				'submenu'    => true,
			),
			'sections' =>
			array(
				array(
					'id'    => 'settings_section',
					'title' => __('Settings', 'pmi-users-sync'),
					'desc'  => __('Settings for PMI Users Sync plugin', 'pmi-users-sync'),
				),
			),
			'fields'   => array(
				'settings_section' => array(
					array(
						'id'    => 'overwrite_pmi_id',
						'label' => 'PMI-ID Priority',
						'desc' => __('If checked, the PMI ID inserted by the users will be overwritten', 'pmi-users-sync'),
						'type'  => 'checkbox',
					),
					array(
						'id'    => 'pmi_id_custom_field',
						'label' => __('PMI-ID custom field', 'pmi-users-sync'),
						'desc' => __('Insert the PMI-ID custom field defined with ACF plugin (e.g. dbem_pmi_id)'),
						'type'  => 'text',
					),
					array(
						'id'          => 'pmi_file_field_id',
						'label'       => __('File', 'pmi-users-sync'),
						'desc'        => __('The Excel file with the PMI-ID extracted from PMI', 'pmi-users-sync'),
						'type'        => 'file',
						'default'     => '',
						'placeholder' => __('Textarea placeholder', 'pmi-users-sync'),
						'options'     => array( //					'btn' => 'Get it'
						)
					),
				),
			),
			'links'    => array(
				'plugin_basename' => plugin_basename(__FILE__),
				'action_links'    => array(
					array(
						'type' => 'default',
						'text' => __('Settings', 'pmi-users-sync'),
					),
					array(
						'type' => 'external',
						'text' => __('Github Repository', 'pmi-users-sync'),
						'url'  => 'https://github.com/angelochillemix/pmi-users-sync',
					),
				),
			),
		);

		/**
		 * Building the settings menu creating a new instance of the {@see Boo_Settings_Helper} class
		 */
		$settings_helper = new Boo_Settings_Helper($config_array_menu);
	}

	/**
	 * Shows the list of users from the Excel file
	 *
	 * @return void
	 */
	public function pmi_users_list_page($args)
	{
		$pmi_file_url = get_option(PMI_USERS_SYNC_PREFIX . 'pmi_file_field_id');


		// Return false if the plugin setting is not set
		if (false !== $pmi_file_url) {
			$file_path = Path_Utils::get_file_path($pmi_file_url);
			$loader = new Pmi_Users_Sync_Pmi_User_Excel_File_Loader($file_path);
			try {
				$users = $loader->load();

				if (isset($_POST['update_users'])) {
					$this->pmi_users_sync_users_update($users);
				}
			} catch (Exception $exception) {
				Pmi_Users_Sync_Logger::logError(__('An error occurred while running the scheduled update. Error is: ') . $exception->getMessage(), null);
			}
		} else {
			$error_message = __('No file has been set in the plugin settings page.');
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
		Pmi_Users_Sync_User_Updater::update($users, $options);
	}
}
