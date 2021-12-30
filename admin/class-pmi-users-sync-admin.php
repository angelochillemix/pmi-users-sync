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
class Pmi_Users_Sync_Admin {


	private const FIELD_ID_OVERWRITE_PMI_ID = 'overwrite_pmi_id';
	public const OPTION_OVERWRITE_PMI_ID    = PMI_USERS_SYNC_PREFIX . self::FIELD_ID_OVERWRITE_PMI_ID;

	private const FIELD_ID_PMI_ID_CUSTOM_FIELD = 'pmi_id_custom_field';
	public const OPTION_PMI_ID_CUSTOM_FIELD    = PMI_USERS_SYNC_PREFIX . self::FIELD_ID_PMI_ID_CUSTOM_FIELD;

	private const FIELD_ID_PMI_FILE_FIELD_ID = 'pmi_file_field_id';
	public const OPTION_PMI_FILE_FIELD_ID    = PMI_USERS_SYNC_PREFIX . self::FIELD_ID_PMI_FILE_FIELD_ID;

	private const FIELD_ID_USER_LOADER = 'user_loader_field';
	public const OPTION_USER_LOADER    = PMI_USERS_SYNC_PREFIX . self::FIELD_ID_USER_LOADER;

	private const FIELD_ID_DEP_SERVICE_USERNAME = 'depservice_username_field';
	public const OPTION_DEP_SERVICE_USERNAME    = PMI_USERS_SYNC_PREFIX . self::FIELD_ID_DEP_SERVICE_USERNAME;

	private const FIELD_ID_DEP_SERVICE_PASSWORD = 'depservice_password_field';
	public const OPTION_DEP_SERVICE_PASSWORD    = PMI_USERS_SYNC_PREFIX . self::FIELD_ID_DEP_SERVICE_PASSWORD;

	public const OPTION_USER_LOADER_EXCEL = 'option_excel';
	public const OPTION_USER_WEB_SERVICE  = 'option_web_service';


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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pmi-users-sync-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pmi-users-sync-admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Callback to notify the users about missing ACF plugin or missing custom field configuration
	 *
	 * @return void
	 */
	public function notify_user_about_acf_plugin() {
		if ( ! is_plugin_active( 'advanced-custom-fields/acf.php' ) ) {
			// Inform the user that this plugin needs ACF to create the custome PMI-ID field that will be added to the user's information.
			ob_start() ?>
			<div class="notice notice-warning is-dismissible">
				<p><strong>Warning:&nbsp;</strong><?php esc_html_e( 'This plugin requires the plugin ', 'pmi-users-sync' ); ?><a href="https://wordpress.org/plugins/advanced-custom-fields/"><?php esc_html_e( 'Advanced Custom Fields', 'pmi-users-sync' ); ?></a></p>
				<p><?php esc_html_e( 'Install the plugin, create a custom field and set its name in the Settings page option "PMI-ID custom field"', 'pmi-users-sync' ); ?></p>
			</div>
			<?php
			echo ob_get_clean();
			return;
		}

		// ACF plugin is installed and active.
		// It is now safe to check that custom field exists.

		if ( ! $this->acf_field_exists( get_option( PMI_USERS_SYNC_PREFIX . 'pmi_id_custom_field' ) ) ) {
			// Inform the user that the ACF field for the PMI-ID is not yet defined.
			ob_start()
			?>
			<div class="notice notice-warning is-dismissible">
				<p><strong>Warning:&nbsp;</strong><?php esc_html_e( 'This plugin requires that a custom user field representing the PMI-ID is defined ', 'pmi-users-sync' ); ?></p>
				<p><?php esc_html_e( 'Install the ACF plugin, create a custom field and set its name in the Settings page option "PMI-ID custom field"', 'pmi-users-sync' ); ?></p>
			</div>
			<?php
			echo ob_get_clean();
		}
	}

	/**
	 * Check if a Advanced Custom Field is defined
	 *
	 * @param string $field_name The name of the field to check existence for.
	 * @return bool true if the field is found, false otherwise
	 */
	private function acf_field_exists( $field_name ) {
		global $wpdb;
		$acf_fields = $wpdb->get_results( $wpdb->prepare( "SELECT ID,post_parent,post_name FROM $wpdb->posts WHERE post_excerpt=%s AND post_type=%s", $field_name, 'acf-field' ) );
		if ( is_null( $acf_fields ) ) {
			return false;
		}
		return ( count( $acf_fields ) ) > 0;
	}


	/**
	 * Build the admin menu using the {@see Boo_Settings_Helper} class
	 *
	 * @see https://github.com/boospot/boo-settings-helper
	 *
	 * @return void
	 */
	public function add_menu_link() {
		/**
		 * Adding the main menu item at admin menu level
		 */
		$menu_page = add_menu_page( esc_html__( 'PMI Users Sync', 'pmi-users-sync' ), esc_html__( 'PMI Users Sync', 'pmi-users-sync' ), 'manage_options', PMI_USERS_SYNC_PREFIX . 'pmi_users_sync_options', array( $this, 'pmi_users_list_page' ), 'dashicons-id-alt' );
		add_submenu_page( PMI_USERS_SYNC_PREFIX . 'pmi_users_sync_options', esc_html__( 'PMI Users', 'pmi-users-sync' ), esc_html__( 'PMI Users', 'pmi-users-sync' ), 'manage_options', PMI_USERS_SYNC_PREFIX . 'pmi_users_sync_options' );

		/**
		 * Build the menu configuration array for the Boo Settings Helper class
		 */
		$config_array_menu = array(
			'prefix'   => PMI_USERS_SYNC_PREFIX,
			'tabs'     => false,
			'menu'     =>
			array(
				'page_title' => __( 'PMI Users Sync Settings', 'pmi-users-sync' ),
				'menu_title' => __( 'Settings', 'pmi-users-sync' ),
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
					'id'    => 'general_settings_section',
					'title' => __( 'General Settings', 'pmi-users-sync' ),
					'desc'  => __( 'Settings for PMI Users Sync plugin', 'pmi-users-sync' ),
				),
				array(
					'id'    => 'loader_settings_section',
					'title' => __( 'Users Loader Settings', 'pmi-users-sync' ),
					'desc'  => __( 'Settings for PMI Users loader', 'pmi-users-sync' ),
				),
			),
			'fields'   => array(
				'general_settings_section' => array(
					array(
						'id'    => self::FIELD_ID_OVERWRITE_PMI_ID,
						'label' => 'PMI-ID Priority',
						'desc'  => __( 'If checked, the PMI ID inserted by the users will be overwritten', 'pmi-users-sync' ),
						'type'  => 'checkbox',
					),
					array(
						'id'    => self::FIELD_ID_PMI_ID_CUSTOM_FIELD,
						'label' => __( 'PMI-ID custom field', 'pmi-users-sync' ),
						'desc'  => __( 'Insert the PMI-ID custom field defined with ACF plugin (e.g. dbem_pmi_id)', 'pmi-users-sync' ),
						'type'  => 'text',
					),
				),
				'loader_settings_section'  => array(
					array(
						// ----
						'id'      => self::FIELD_ID_USER_LOADER,
						'label'   => __( 'Loader', 'pmi-users-sync' ),
						'desc'    => __( 'The type of loader to use to load the PMI members', 'pmi-users-sync' ),
						'type'    => 'select',
						'default' => self::OPTION_USER_LOADER_EXCEL,
						'options' => array(
							self::OPTION_USER_LOADER_EXCEL => 'Excel',
							self::OPTION_USER_WEB_SERVICE  => 'Web Service',
						),
					),
					array(
						'id'          => self::FIELD_ID_PMI_FILE_FIELD_ID,
						'label'       => __( 'File', 'pmi-users-sync' ),
						'desc'        => __( 'The Excel file with the PMI-ID extracted from PMI', 'pmi-users-sync' ),
						'type'        => 'file',
						'default'     => '',
						'placeholder' => __( 'Insert the Excel file path', 'pmi-users-sync' ),
					),
					array(
						'id'          => self::FIELD_ID_DEP_SERVICE_USERNAME,
						'label'       => __( 'DEPService username', 'pmi-users-sync' ),
						'desc'        => __( 'The username to access the PMI DEPService. Contact your PMI Chapter Officer to get it.', 'pmi-users-sync' ),
						'type'        => 'text',
						'default'     => '',
						'placeholder' => __( 'Insert the username', 'pmi-users-sync' ),
					),
					array(
						'id'                => self::FIELD_ID_DEP_SERVICE_PASSWORD,
						'label'             => __( 'DEPService password', 'pmi-users-sync' ),
						'desc'              => __( 'The password to access the PMI DEPService. Contact your PMI Chapter Officer to get it.', 'pmi-users-sync' ),
						'type'              => 'password',
						'default'           => '',
						'placeholder'       => __( 'Insert the password', 'pmi-users-sync' ),
						'sanitize_callback' => array( $this, 'sanitize_depservice_password' ),
					),
				),
			),
			'links'    => array(
				'plugin_basename' => plugin_basename( __FILE__ ),
				'action_links'    => array(
					array(
						'type' => 'default',
						'text' => __( 'Settings', 'pmi-users-sync' ),
					),
					array(
						'type' => 'external',
						'text' => __( 'Github Repository', 'pmi-users-sync' ),
						'url'  => 'https://github.com/angelochillemix/pmi-users-sync',
					),
				),
			),
		);

		/**
		 * Building the settings menu creating a new instance of the {@see Boo_Settings_Helper} class
		 */
		$settings_helper = new Boo_Settings_Helper( $config_array_menu );
	}

	/**
	 * Overrides the default sanitize function that returns a hash of the password which will not work with the web service call
	 *
	 * @param string $value The password set in the settings page.
	 * @return string The same password
	 */
	public function sanitize_depservice_password( $value ) {
		return $value;
	}

	/**
	 * Shows the list of users from the Excel file
	 *
	 * @param mixed $args Arguments of the callback.
	 * @return void
	 */
	public function pmi_users_list_page( $args ) {
		try {
			$loader                     = Pmi_Users_Sync_User_Loader_Factory::create_user_loader();
			$users                      = $loader->load();
			$pmi_id_custom_field_exists = $this->acf_field_exists( get_option( self::OPTION_PMI_ID_CUSTOM_FIELD ) );
			if ( ! $pmi_id_custom_field_exists ) {
				$error_message = __( 'PMI-ID custom field does not exist. Update not done!', 'pmi-users-sync' );
			}

			if ( isset( $_POST['update_users'] ) && $pmi_id_custom_field_exists ) {
				Pmi_Users_Sync_Logger::logInformation( __( 'Synchronizing the PMI-ID of the users', 'pmi-users-sync' ) );
				$this->pmi_users_sync_users_update( $users );
				$error_message = __( 'Users successfully updated!', 'pmi-users-sync' );
			}
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
			Pmi_Users_Sync_Logger::logError( __( 'An error occurred while rendering the page. Error is: ', 'pmi-users-sync' ) . $exception->getMessage() );
			$error_message = __( 'An error occurred during the page rendering', 'pmi-users-sync' ) . ' ' . $exception->getMessage();
		}
		$user_loader_type = get_option( self::OPTION_USER_LOADER );
		$file_path        = self::OPTION_USER_LOADER_EXCEL === $user_loader_type ? get_option( self::OPTION_PMI_FILE_FIELD_ID ) : false;

		require_once plugin_dir_path( __FILE__ ) . 'partials/pmi-users-sync-admin-display.php';
	}

	/**
	 * Synchronize the PMI-ID of the users.
	 *
	 * @param Pmi_Users_Sync_Pmi_User[] $users List of users retrieved with the loader.
	 * @return void
	 */
	private function pmi_users_sync_users_update( $users ) {
		$options = array();
		$options = array(
			self::OPTION_OVERWRITE_PMI_ID    => get_option( self::OPTION_OVERWRITE_PMI_ID ),
			self::OPTION_PMI_ID_CUSTOM_FIELD => get_option( self::OPTION_PMI_ID_CUSTOM_FIELD ),
		);
		Pmi_Users_Sync_User_Updater::update( $users, $options );
	}
}
