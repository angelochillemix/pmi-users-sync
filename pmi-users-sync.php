<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://angelochillemi.com/pmi-users-sync
 * @since             1.0.0
 * @package           Pmi_Users_Sync
 *
 * @wordpress-plugin
 * Plugin Name:       PMI Users Sync
 * Plugin URI:        http://angelochillemi.com/pmi-users-sync
 * Description:       Synchronize the PMI subscribed users with WordPress users, using ACF for the PMI-ID. This plugin is particularly useful for PMI Chapters to offer specific services to PMI subscribed members.
 * Version:           1.4.1
 * Author:            Angelo Chillemi
 * Author URI:        http://angelochillemi.com
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pmi-users-sync
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Prefix to use for options, database keys, etc. to make them unique
 */
define( 'PMI_USERS_SYNC_PREFIX', 'pus_' );

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PMI_USERS_SYNC_VERSION', '1.4.1' );

/**
 * The directory path of the plugin
 */
define( 'PMI_USERS_SYNC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * The plugin folder URL
 */
define( 'PMI_USERS_SYNC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The path of the admin directory
 */
define( 'PMI_USERS_SYNC_PLUGIN_DIR_ADMIN', plugin_dir_path( __FILE__ ) . 'admin/' );

/**
 * The path of the resources directory
 */
define( 'PMI_USERS_SYNC_PLUGIN_DIR_RESOURCES', plugin_dir_path( __FILE__ ) . 'resources/' );

/**
 * The path of the includes directory
 */
define( 'PMI_USERS_SYNC_PLUGIN_DIR_INCLUDES', plugin_dir_path( __FILE__ ) . 'includes/' );

/**
 * The path of the includes directory
 */
define( 'PMI_USERS_SYNC_PLUGIN_DIR_VENDOR', plugin_dir_path( __FILE__ ) . 'vendor/' );


/**
 * Represents the hook function to setup the cron for the regular updated of the PMI-ID
 */
define( 'PMI_USERS_SYNC_CRON_HOOK', PMI_USERS_SYNC_PREFIX . 'update_users_pmi_id' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pmi-users-sync-activator.php
 */
function pmi_users_sync_activate_pmi_users_sync() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pmi-users-sync-activator.php';
	Pmi_Users_Sync_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pmi-users-sync-deactivator.php
 */
function pmi_users_sync_deactivate_pmi_users_sync() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pmi-users-sync-deactivator.php';
	Pmi_Users_Sync_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'pmi_users_sync_activate_pmi_users_sync' );
register_deactivation_hook( __FILE__, 'pmi_users_sync_deactivate_pmi_users_sync' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-pmi-users-sync.php';

/**
 * Includes the autoload for the vendor packages from composer
 */
require dirname( __FILE__ ) . '/vendor/autoload.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function pmi_users_sync_run_pmi_users_sync() {
	$plugin = new Pmi_Users_Sync();
	$plugin->run();
}
pmi_users_sync_run_pmi_users_sync();
