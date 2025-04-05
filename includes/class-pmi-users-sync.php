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
	 * The schedule responsible for scheduling the synchronization of PMI-ID of PMI members with the users
	 * registered to the WordPress website.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Pmi_Users_Sync_Cron_Scheduler $scheduler Scheduls the synchronization of PMI-ID of PMI members with the users registered to the WordPress website.
	 */
	protected $scheduler = null;

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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/interface-pmi-users-sync-user-loader.php';

		/**
		 * The class responsible for loading the PMI users from the Excel file from PMI
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pmi-users-sync-pmi-user-excel-file-loader.php';

		/**
		 * The class responsible for loading the PMI users through PMI DPE Web Service
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pmi-users-sync-pmi-user-web-service-loader.php';

		/**
		 * The class responsible to create the user loader based on the plugin settings
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pmi-users-sync-user-loader-factory.php';

		/**
		 * The class responsible for loading the PMI users with a call to PMI DPE Web Service
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/class-pmi-users-sync-members-web-service.php';

		/**
		 * The abstract class responsible to update the PMI users with PMI-ID of the Excel file from PMI
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/class-pmi-users-sync-user-abstract-updater.php';

		/**
		 * The class responsible to synchronize the WP users with PMI users
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/class-pmi-users-sync-user-updater.php';

		/**
		 * The class responsible to synchronize the WP users with PMI users
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/class-pmi-users-sync-user-memberships-roles-updater.php';

		/**
		 * The class responsible to log messages to the log file
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/class-pmi-users-sync-logger.php';

		/**
		 * The class to setup the cron scheduler
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/class-pmi-users-sync-cron-scheduler.php';

		/**
		 * The class with Path utilities
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/class-pmi-users-sync-path-utils.php';

		/**
		 * The class with utilities methods
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/class-pmi-users-sync-utils.php';

		/**
		 * Represents the abstract class of the the classes responsible to synchronize and update the user attributes between WP users and PMI users.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/class-pmi-users-sync-user-attribute-updater.php';

		/**
		 * Represents the abstract class of the the factory class responsible to create user attributes updater instances.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/class-pmi-users-sync-user-attribute-updater-factory.php';

		/**
		 * The class responsible to synchronize and update the PMI-ID.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/class-pmi-users-sync-user-pmi-id-updater.php';

		/**
		 * The class responsible to synchronize and update the roles.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/class-pmi-users-sync-user-roles-updater.php';

		/**
		 * The class responsible to synchronize and update the memberships.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/class-pmi-users-sync-user-memberships-updater.php';

		/**
		 * The class responsible to synchronize and update the memberships.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/class-pmi-users-sync-user-membership-roles-mapping-updater.php';

		/**
		 * The class responsible to synchronize and update the memberships.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/class-pmi-users-sync-acf-helper.php';

		/**
		 * The class responsible to synchronize and update the memberships.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/class-pmi-users-sync-user-updater-factory.php';

		$this->loader    = new Pmi_Users_Sync_Loader();
		$this->scheduler = new Pmi_Users_Sync_Cron_Scheduler();
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
		$plugin_i18n = new Pmi_Users_Sync_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @throws InvalidArgumentException Throws an exception if the scheduler instance is null.
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Pmi_Users_Sync_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		/**
		 * Add the admin menu for the plugin
		 */
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu_link' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'notify_user_about_acf_plugin' );

		$this->loader->add_filter( 'plugin_row_meta', $plugin_admin, 'filter_plugin_row_meta', 10, 4 );

		if ( null === $this->scheduler ) {
			throw new InvalidArgumentException( 'Schedule instance cannot be null', 1 );
		}

		/**
		 * Register the scheduled event to synchronize the PMI-ID on a regular basis.
		 */
		$this->loader->add_filter( 'cron_schedules', $this->scheduler, Pmi_Users_Sync_Cron_Scheduler::PMI_USERS_SYNC_CRON_CUSTOM_SCHEDULE_CALLBACK );
		$this->loader->add_action( Pmi_Users_Sync_Cron_Scheduler::PMI_USERS_SYNC_CRON_HOOK, $this->scheduler, Pmi_Users_Sync_Cron_Scheduler::PMI_USERS_SYNC_CRON_SCHEDULED_CALLBACK );

		$this->loader->add_action( 'user_register', $plugin_admin, 'execute_update_users_membership_role_map' );
		$this->loader->add_action( 'profile_update', $plugin_admin, 'execute_update_users_membership_role_map', 10 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Pmi_Users_Sync_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	/**
	 * Schedule the synchornization of PMI-ID
	 *
	 * @return void
	 */
	public function schedule_synchronization() {
		// Activate the cron scheduler to synchronize the PMI-ID from PMI with the users registered to the site.
		$recurrence = get_option( Pmi_Users_Sync_Admin::OPTION_LOADER_SCHEDULE );
		if ( false === $recurrence ) {
			$recurrence = Pmi_Users_Sync_Cron_Scheduler::PMI_USERS_SYNC_CRON_SCHEDULE_MONTHLY;
		}
		$this->scheduler->schedule( $recurrence );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();

		// Schedule the cron task for the synchornization of the PMI-ID.
		// Task to be executed after the loader run to ensure hooks and actions are set.
		$this->schedule_synchronization();
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
