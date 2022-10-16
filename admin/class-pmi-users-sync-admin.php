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

use phpDocumentor\Reflection\Types\String_;

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

	/**
	 * The error message displayed to the user
	 *
	 * @var string
	 */
	private string $pus_error_message;

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

	private const FIELD_ID_LOADER_SCHEDULE = 'loader_schedule_field';
	public const OPTION_LOADER_SCHEDULE    = PMI_USERS_SYNC_PREFIX . self::FIELD_ID_LOADER_SCHEDULE;

	private const FIELD_ID_USER_ROLE = 'user_role_field';
	public const OPTION_USER_ROLE    = PMI_USERS_SYNC_PREFIX . self::FIELD_ID_USER_ROLE;

	private const FIELD_ID_USER_ROLE_TO_REMOVE = 'user_role_to_remove_field';
	public const OPTION_USER_ROLE_TO_REMOVE    = PMI_USERS_SYNC_PREFIX . self::FIELD_ID_USER_ROLE_TO_REMOVE;

	private const FIELD_ID_MEMBERSHIP_CUSTOM_FIELD = 'membership_custom_field';
	public const OPTION_MEMBERSHIP_CUSTOM_FIELD    = PMI_USERS_SYNC_PREFIX . self::FIELD_ID_MEMBERSHIP_CUSTOM_FIELD;


	private const FIELD_ID_USER_MEMBERSHIPS = 'user_memberships_field';
	public const OPTION_MEMBERSHIP          = PMI_USERS_SYNC_PREFIX . self::FIELD_ID_USER_MEMBERSHIPS;

	private const FIELD_ID_USER_MEMBERSHIPS_TO_REMOVE = 'user_memberships_to_remove_field';
	public const OPTION_MEMBERSHIP_TO_REMOVE          = PMI_USERS_SYNC_PREFIX . self::FIELD_ID_USER_MEMBERSHIPS_TO_REMOVE;

	public const LOADER_LAST_SYNCHRONIZATION_DATE_TIME = PMI_USERS_SYNC_PREFIX . 'loader_last_synchronization_date_time';

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
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pmi-users-sync-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
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

		if (
			! Pmi_Users_Sync_Utils::acf_field_exists( get_option( self::OPTION_PMI_ID_CUSTOM_FIELD ) )
			|| ! Pmi_Users_Sync_Utils::acf_field_exists( get_option( self::OPTION_MEMBERSHIP_CUSTOM_FIELD ) )
		) {
			// Inform the user that the ACF field for the PMI-ID is not yet defined.
			ob_start()
			?>
			<div class="notice notice-warning is-dismissible">
				<p><strong>Warning:&nbsp;</strong><?php esc_html_e( 'PMI Users Sync plugin requires that a custom membership and custom user field representing the PMI-ID are defined', 'pmi-users-sync' ); ?></p>
				<p><?php esc_html_e( 'Install the ACF plugin, create the custom fields and set their name in the Settings page option "PMI-ID custom field" and "Membership custom field"', 'pmi-users-sync' ); ?></p>
			</div>
			<?php
			echo ob_get_clean();
		}
	}

	/**
	 * Filters the array of row meta for each plugin in the Plugins list table.
	 *
	 * @param string[] $plugin_meta An array of the plugin's metadata.
	 * @param string   $plugin_file Path to the plugin file relative to the plugins directory.
	 * @return string[] An array of the plugin's metadata.
	 */
	public function filter_plugin_row_meta( array $plugin_meta, $plugin_file ) {
		if ( 'pmi-users-sync/pmi-users-sync.php' !== $plugin_file ) {
			return $plugin_meta;
		}

		$plugin_meta[] = sprintf(
			'<a href="%1$s"><span class="dashicons dashicons-awards" aria-hidden="true" style="font-size:14px;line-height:1.3"></span>%2$s</a>',
			'https://paypal.me/angelochillemi',
			esc_html_x( 'Donate', 'verb', 'pmi-users-sync' )
		);
		return $plugin_meta;
	}


	/**
	 * Build the admin menu using the {@see Boo_Settings_Helper} class
	 *
	 * @see https://github.com/boospot/boo-settings-helper
	 *
	 * @return void
	 */
	public function add_menu_link() {
		// Adding the main menu item at admin menu level.
		add_menu_page( esc_html__( 'PMI Users Sync', 'pmi-users-sync' ), esc_html__( 'PMI Users Sync', 'pmi-users-sync' ), 'manage_options', PMI_USERS_SYNC_PREFIX . 'pmi_users_sync_options', array( $this, 'pmi_users_list_page' ), 'dashicons-id-alt' );
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
						'id'       => self::FIELD_ID_USER_ROLE,
						'label'    => __( 'Role to set', 'pmi-users-sync' ),
						'callback' => array( $this, 'pmi_users_sync_roles_render_field' ),
						'type'     => 'multicheck',
					),
					array(
						'id'       => self::FIELD_ID_USER_ROLE_TO_REMOVE,
						'label'    => __( 'Role to remove', 'pmi-users-sync' ),
						'callback' => array( $this, 'pmi_users_sync_roles_render_field' ),
						'type'     => 'multicheck',
					),
					array(
						'id'    => self::FIELD_ID_PMI_ID_CUSTOM_FIELD,
						'label' => __( 'PMI-ID custom field', 'pmi-users-sync' ),
						'desc'  => __( 'Insert the PMI-ID custom field defined with ACF plugin (e.g. dbem_pmi_id)', 'pmi-users-sync' ),
						'type'  => 'text',
					),
					array(
						'id'       => self::FIELD_ID_USER_MEMBERSHIPS,
						'label'    => __( 'Memberships to set', 'pmi-users-sync' ),
						'callback' => array( $this, 'pmi_users_sync_memberships_render_field' ),
						'type'     => 'multicheck',
					),
					array(
						'id'       => self::FIELD_ID_USER_MEMBERSHIPS_TO_REMOVE,
						'label'    => __( 'Memberships to remove', 'pmi-users-sync' ),
						'callback' => array( $this, 'pmi_users_sync_memberships_render_field' ),
						'type'     => 'multicheck',
					),
					array(
						'id'    => self::FIELD_ID_MEMBERSHIP_CUSTOM_FIELD,
						'label' => __( 'Membership custom field', 'pmi-users-sync' ),
						'desc'  => __( 'Insert the Membership custom field defined with ACF plugin (e.g. dbem_membership)', 'pmi-users-sync' ),
						'type'  => 'text',
					),
				),
				'loader_settings_section'  => array(
					array(
						// ----
						'id'      => self::FIELD_ID_LOADER_SCHEDULE,
						'label'   => __( 'Synchonization schedule', 'pmi-users-sync' ),
						'desc'    => __( 'The recurrence of the users loader that synchronizes the PMI-ID', 'pmi-users-sync' ),
						'type'    => 'select',
						'default' => Pmi_Users_Sync_Cron_Scheduler::PMI_USERS_SYNC_CRON_SCHEDULE_DEFAULT,
						'options' => array(
							Pmi_Users_Sync_Cron_Scheduler::PMI_USERS_SYNC_CRON_SCHEDULE_DAILY => __( 'Daily', 'pmi-users-sync' ),
							Pmi_Users_Sync_Cron_Scheduler::PMI_USERS_SYNC_CRON_SCHEDULE_WEEKLY => __( 'Weekly', 'pmi-users-sync' ),
							Pmi_Users_Sync_Cron_Scheduler::PMI_USERS_SYNC_CRON_SCHEDULE_MONTHLY  => __( 'Monthly', 'pmi-users-sync' ),
							Pmi_Users_Sync_Cron_Scheduler::PMI_USERS_SYNC_CRON_SCHEDULE_QUARTERLY  => __( 'Quarterly', 'pmi-users-sync' ),
						),
					),
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
						'desc'        => __( 'The Excel file path with the PMI-ID extracted from PMI.</br>Once inserted, it just needs to be overwritten manually or by batch.</br>Insert the file if Excel file is selected in the <strong>Loader</strong> setting field.', 'pmi-users-sync' ),
						'type'        => 'file',
						'default'     => '',
						'placeholder' => __( 'Insert the Excel file path', 'pmi-users-sync' ),
						'options'     => array(
							'btn' => __( 'Set', 'pmi-users-sync' ),
						),
					),
					array(
						'id'          => self::FIELD_ID_DEP_SERVICE_USERNAME,
						'label'       => __( 'DEPService username', 'pmi-users-sync' ),
						'desc'        => __( 'The username to access the PMI DEPService. Contact your PMI Chapter Officer to get it.</br><strong>Note:</strong> Set the username if Web Service is selected in the <strong>Loader</strong> setting field', 'pmi-users-sync' ),
						'type'        => 'text',
						'default'     => '',
						'placeholder' => __( 'Insert the username', 'pmi-users-sync' ),
					),
					array(
						'id'                => self::FIELD_ID_DEP_SERVICE_PASSWORD,
						'label'             => __( 'DEPService password', 'pmi-users-sync' ),
						'desc'              => __( 'The password to access the PMI DEPService. Contact your PMI Chapter Officer to get it.</br><strong>Note:</strong> Set the password if Web Service is selected in the <strong>Loader</strong> setting field', 'pmi-users-sync' ),
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
	 * Callback function to render the list of roles
	 *
	 * @param array $args The arguments from the Boo Settings Helper.
	 * @return void
	 */
	public function pmi_users_sync_roles_render_field( $args ) {
		$all_roles = wp_roles()->roles;
		$value     = $args['value'];
		if ( empty( $value ) ) {
			$value = $args['default'];
		}

		$html = '<fieldset>';
		foreach ( $all_roles as $role => $role_config ) {
			$checked = isset( $value[ $role ] ) ? $value[ $role ] : '0';
			$html   .= sprintf( '<label for="%1$s[%2$s][%3$s]">', $args['section'], $args['id'], $role );
			$html   .= sprintf( '<input type="checkbox" class="checkbox" id="%1$s[%2$s][%3$s]" name="%5$s[%3$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $role, checked( $checked, $role, false ), $args['name'] );
			$html   .= sprintf( '%1$s</label><br>', $role_config['name'] );
		}

		switch ( $args['id'] ) {
			case self::FIELD_ID_USER_ROLE:
				$html .= __( 'The user role to set if user is found member of PMI', 'pmi-users-sync' );
				break;
			case self::FIELD_ID_USER_ROLE_TO_REMOVE:
				$html .= __( 'The user role to remove if user is not found member of PMI', 'pmi-users-sync' );
				break;
			default:
				break;
		}
		$html .= '</fieldset>';
		echo $html;
		unset( $all_roles, $html );
	}

	/**
	 * Callback function to render the list of memberships from the Advanced Custom Fields plugin.
	 *
	 * @param array $args The arguments from the Boo Settings Helper.
	 * @return void
	 * @since 1.3.0
	 */
	public function pmi_users_sync_memberships_render_field( $args ) {
		$all_memberships = Pmi_Users_Sync_Acf_Helper::get_memberships_settings();

		$value = $args['value'];
		if ( empty( $value ) ) {
			$value = $args['default'];
		}

		$html = '<fieldset>';
		foreach ( $all_memberships as $membership => $membership_config ) {
			$checked = isset( $value[ $membership ] ) ? $value[ $membership ] : '0';
			$html   .= sprintf( '<label for="%1$s[%2$s][%3$s]">', $args['section'], $args['id'], $membership );
			$html   .= sprintf( '<input type="checkbox" class="checkbox" id="%1$s[%2$s][%3$s]" name="%5$s[%3$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $membership, checked( $checked, $membership, false ), $args['name'] );
			$html   .= sprintf( '%1$s</label><br>', $membership_config );
		}

		switch ( $args['id'] ) {
			case self::FIELD_ID_USER_MEMBERSHIPS:
				$html .= __( 'The user membership to set if user is found member of PMI', 'pmi-users-sync' );
				break;
			case self::FIELD_ID_USER_MEMBERSHIPS_TO_REMOVE:
				$html .= __( 'The user membership to remove if user is not found member of PMI', 'pmi-users-sync' );
				break;
			default:
				break;
		}
		$html .= '</fieldset>';
		echo $html;
		unset( $all_memberships, $html );
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
	 * Load the users from the selected source (Excel file, Web Service, etc.)
	 *
	 * @return array The list of users
	 */
	private function load_users() {
		$pus_users          = array(); // Initialize as an empty array.
		$user_loader_option = get_option( self::OPTION_USER_LOADER ); // Check that the loader option is set.

		Pmi_Users_Sync_Logger::log_information( 'Checking user loader option.' );

		if ( ! $user_loader_option ) {
			$this->add_error_message( __( 'Loader not set yet. Please set it up first in the Settings page.', 'pmi-users-sync' ) );
		}

		if ( $user_loader_option ) {
			Pmi_Users_Sync_Logger::log_information( 'Loading the users.' );
			try {
				$pus_users = Pmi_Users_Sync_User_Loader_Factory::create_user_loader()->load();
			} catch ( \PhpOffice\PhpSpreadsheet\Reader\Exception $exception ) {
				Pmi_Users_Sync_Logger::log_error( __( 'An error occurred while reading the Excel file. Error is: ', 'pmi-users-sync' ) . $exception->getMessage() );
				$this->add_error_message( __( 'No file has been set in the plugin settings page or file does not exist.', 'pmi-users-sync' ) );
			} catch ( SoapFault $fault ) {
				Pmi_Users_Sync_Logger::log_error( __( 'An error occurred while retrieving the list of PMI members through the web service. Error is: ', 'pmi-users-sync' ) . $fault->faultstring );
				$this->add_error_message( __( 'An error occurred while retrieving the list of PMI members through the web service.', 'pmi-users-sync' ) );
			} catch ( InvalidArgumentException $exception ) {
				Pmi_Users_Sync_Logger::log_error( __( 'An error occurred. Error is: ', 'pmi-users-sync' ) . $exception->getMessage() );
				$this->add_error_message( __( 'An error occurred', 'pmi-users-sync' ) . ' ' . $exception->getMessage() );
			} catch ( Exception $exception ) {
				$this->add_error_message( __( 'An error occurred during the page rendering', 'pmi-users-sync' ) );
				Pmi_Users_Sync_Logger::log_error( __( 'An error occurred while rendering the page. Error is: ', 'pmi-users-sync' ) . isnull( $exception ) ? '' : $exception->getMessage() );
			}
		}
		return $pus_users;
	}

	/**
	 * Update the users list
	 *
	 * @param Pmi_Users_Sync_Pmi_User[] $pus_users The array of users from PMI to synchronize with the WordPress users.
	 * @return void
	 */
	private function update_users( $pus_users ) {
		Pmi_Users_Sync_Logger::log_information( 'Checking if ACF fields exist.' );
		$pmi_id_custom_field_exists     = false;
		$membership_custom_field_exists = false;

		try {
			$pmi_id_custom_field_exists = Pmi_Users_Sync_Utils::acf_field_exists( get_option( self::OPTION_PMI_ID_CUSTOM_FIELD ) );
			if ( ! $pmi_id_custom_field_exists ) {
				$this->add_error_message( __( 'PMI-ID custom field does not exist. Update not done!', 'pmi-users-sync' ) );
			}
			$membership_custom_field_exists = Pmi_Users_Sync_Utils::acf_field_exists( get_option( self::OPTION_MEMBERSHIP_CUSTOM_FIELD ) );
			if ( ! $membership_custom_field_exists ) {
				$this->add_error_message( __( 'Membership custom field does not exist. Update not done!', 'pmi-users-sync' ) );
			}
		} catch ( Exception $exception ) {
			$this->add_error_message( __( 'An error occurred while checking ACF fields.', 'pmi-users-sync' ) . $exception->getMessage() );
			Pmi_Users_Sync_Logger::log_error( $this->pus_error_message . ' Error is: ', 'pmi-users-sync' ) . $exception->getMessage();
		}

		try {
			// TODO #9 Move the code to an AJAX call to synchronize the users in background.

			if (
				isset( $_POST['update_users'] ) // Update of the PMI-ID triggered manually.
				&& $pmi_id_custom_field_exists
				&& $membership_custom_field_exists
			) {
				Pmi_Users_Sync_Logger::log_information( 'Updating the users.' );
				if (
					! isset( $_POST[ PMI_USERS_SYNC_PREFIX . 'nonce_field' ] )
					|| ! wp_verify_nonce( htmlspecialchars( sanitize_text_field( wp_unslash( $_POST[ PMI_USERS_SYNC_PREFIX . 'nonce_field' ] ) ) ), PMI_USERS_SYNC_PREFIX . 'nonce_action' )
				) {
					Pmi_Users_Sync_Logger::log_error( __( 'Nonce failed!', 'pmi-users-sync' ) );
					wp_nonce_ays( '' );
				}
				Pmi_Users_Sync_Logger::log_information( __( 'Synchronizing the PMI-ID of the users', 'pmi-users-sync' ) );
				Pmi_Users_Sync_User_Updater_Factory::create_user_updater()->update( $pus_users, $this->get_options() );
				$this->pus_error_message .= '\r\n' . __( 'Users successfully updated!', 'pmi-users-sync' );
			}
		} catch ( Exception $exception ) {
			$this->pus_error_message = __( 'An error occurred while updating the users.', 'pmi-users-sync' ) . $exception->getMessage();
			Pmi_Users_Sync_Logger::log_error( $this->pus_error_message . ' Error is: ', 'pmi-users-sync' ) . $exception->getMessage();
		}
	}

	/**
	 * Shows the list of users from the Excel file
	 *
	 * @return void
	 * @throws Exception If the users list is not set.
	 */
	public function pmi_users_list_page() {
		$this->empty_error_message();

		$pus_users = $this->load_users();
		if ( isset( $_POST['update_users'] ) ) { // Update of the PMI-ID triggered manually.
			$this->update_users( $pus_users );
		}

		// Update the last synchronization date and time on the page.
		Pmi_Users_Sync_Logger::log_information( 'Getting last synchronization date.' );
		$pus_last_synchronization_date = get_option( self::LOADER_LAST_SYNCHRONIZATION_DATE_TIME );
		if ( ! $pus_last_synchronization_date || empty( $pus_last_synchronization_date ) ) {
			$pus_last_synchronization_date = __( 'No synchronization occurred yet', 'pmi-users-sync' );
		}
		Pmi_Users_Sync_Logger::log_information( 'Rendering the users list page.' );
		require_once plugin_dir_path( __FILE__ ) . 'partials/pmi-users-sync-admin-display.php';
	}

	/**
	 * Add messages to the error message to be displayed when the page is rendered
	 *
	 * @param string $pus_error_message The message to attach to the error message.
	 * @return string The error message.
	 */
	private function add_error_message( $pus_error_message ): string {
		return $this->pus_error_message .= $pus_error_message . '\r\n';
	}

	/**
	 * Empty the error message to be displayed when the page is rendered
	 */
	private function empty_error_message(): string {
		$this->pus_error_message = '';
		return $this->pus_error_message;
	}

	/**
	 * Returns the options for the synchronization of the PMI user.
	 *
	 * @return array plugin settings
	 */
	private function get_options() {
		return array(
			self::OPTION_OVERWRITE_PMI_ID        => get_option( self::OPTION_OVERWRITE_PMI_ID ),
			self::OPTION_PMI_ID_CUSTOM_FIELD     => get_option( self::OPTION_PMI_ID_CUSTOM_FIELD ),
			self::OPTION_USER_ROLE               => get_option( self::OPTION_USER_ROLE ),
			self::OPTION_USER_ROLE_TO_REMOVE     => get_option( self::OPTION_USER_ROLE_TO_REMOVE ),
			self::OPTION_MEMBERSHIP_CUSTOM_FIELD => get_option( self::OPTION_MEMBERSHIP_CUSTOM_FIELD ),
			self::OPTION_MEMBERSHIP              => get_option( self::OPTION_MEMBERSHIP ),
			self::OPTION_MEMBERSHIP_TO_REMOVE    => get_option( self::OPTION_MEMBERSHIP_TO_REMOVE ),
		);
	}
}
