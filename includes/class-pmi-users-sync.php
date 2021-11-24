<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://angelochillemi.com
 * @since      1.0.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 * @author     Angelo Chillemi <info@angelochillemi.com>
 */
class Pmi_Users_Sync {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Pmi_Users_Sync_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $pmi_users_sync    The string used to uniquely identify this plugin.
	 */
	protected $pmi_users_sync;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PMI_USERS_SYNC_VERSION' ) ) {
			$this->version = PMI_USERS_SYNC_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->pmi_users_sync = 'pmi-users-sync';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Pmi_Users_Sync_Loader. Orchestrates the hooks of the plugin.
	 * - Pmi_Users_Sync_i18n. Defines internationalization functionality.
	 * - Pmi_Users_Sync_Admin. Defines all hooks for the admin area.
	 * - Pmi_Users_Sync_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pmi-users-sync-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pmi-users-sync-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-pmi-users-sync-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-pmi-users-sync-public.php';

		/**
		 * The class responsible for defining the PMI user
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pmi-users-sync-pmi-user.php';

		/**
		 * The interface for defining the loader of the PMI users from a source
		 */
		require_once(plugin_dir_path( dirname( __FILE__ ) ) . 'includes/interface-pmi-users-sync-user-loader.php');

		/**
		 * The class responsible for loading the PMI users from the Excel file from PMI
		 */
		require_once(plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pmi-users-sync-user-excel-file-loader.php');

		/**
		 * The class responsible to update the PMI users with PMI-ID of the Excel file from PMI
		 */
		require_once(plugin_dir_path( dirname( __FILE__ ) ) . '/includes/class-pmi-users-sync-user-updater.php');

		/**
		 * The class responsible to log messages to the log file
		 */
		require_once(plugin_dir_path( dirname( __FILE__ ) ) . '/includes/class-pmi-users-sync-logger.php');

		/**
		 * Includes the global utility functions
		 */
		require_once(plugin_dir_path( dirname( __FILE__ ) ) . '/includes/functions-pmi-users-sync.php');


		//require_once(plugin_dir_path( dirname( __FILE__ ) ) . '/admin/pmi-users-sync-update.php');

		$this->loader = new Pmi_Users_Sync_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Pmi_Users_Sync_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Pmi_Users_Sync_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Pmi_Users_Sync_Admin( $this->get_pmi_users_sync(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		/**
		 * Add the admin menu for the plugin
		 */
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu_link');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Pmi_Users_Sync_Public( $this->get_pmi_users_sync(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_pmi_users_sync() {
		return $this->get_plugin_name();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->pmi_users_sync;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Pmi_Users_Sync_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
